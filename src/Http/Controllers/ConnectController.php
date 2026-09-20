<?php

namespace Qodli\QookieStatamic\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Qodli\QookieStatamic\Support\Connection;
use Statamic\Facades\User;

class ConnectController extends Controller
{
    private function authorizePilot(): string
    {
        abort_unless(config('qookie-statamic.v2_enabled'), 404);
        $user = User::current();
        abort_unless($user && $user->isSuper(), 403);
        return (string) $user->id();
    }

    private function apiBase(): string
    {
        $url = rtrim(config('qookie-statamic.app_url', 'https://app.qookieqloud.com'), '/');
        abort_unless(str_starts_with($url, 'https://'), 500);
        return $url;
    }

    public function start(Request $request)
    {
        $userId = $this->authorizePilot();
        $state = bin2hex(random_bytes(32));
        $verifier = bin2hex(random_bytes(32));
        $callback = route('qookie-statamic.cp.callback');
        $request->session()->put('qookie_connect', [
            'state' => $state, 'verifier' => $verifier, 'user_id' => $userId,
            'callback' => $callback, 'expires_at' => now()->addMinutes(20)->timestamp,
        ]);
        $query = http_build_query([
            'integration' => 'statamic', 'state' => $state, 'redirect_uri' => $callback,
            'code_challenge_method' => 'S256',
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '='),
        ], '', '&', PHP_QUERY_RFC3986);
        return redirect()->away($this->apiBase().'/app/integrations/connect?'.$query)
            ->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function callback(Request $request, Connection $connection)
    {
        $userId = $this->authorizePilot();
        $state = $request->query('state');
        $attempt = $request->session()->get('qookie_connect');
        abort_unless(is_string($state) && $attempt && hash_equals($attempt['state'], $state)
            && $attempt['user_id'] === $userId && $attempt['expires_at'] > now()->timestamp, 403);
        $lock = Cache::lock('qookie-connect:'.hash('sha256', $state), 45);
        abort_unless($lock->get(), 409);
        try {
            // Keep a tombstone so a repeated callback cannot exchange a saved session twice.
            abort_if(Cache::has('qookie-consumed:'.hash('sha256', $state)), 409);
            Cache::put('qookie-consumed:'.hash('sha256', $state), true, now()->addMinutes(25));
            $request->session()->forget('qookie_connect');
            $ok = false;
            $code = $request->query('auth_code');
            if (!$request->has('error') && is_string($code) && preg_match('/\A[a-f0-9]{64}\z/D', $code)) {
                try {
                    $response = Http::acceptJson()->withoutRedirecting()->timeout(15)->post($this->apiBase().'/api/v2/pairing/exchange', [
                        'auth_code' => $code, 'code_verifier' => $attempt['verifier'], 'redirect_uri' => $attempt['callback'],
                    ]);
                    if ($response->successful()) { $connection->save($response->json()); $ok = true; }
                } catch (\Throwable) { /* Do not log credentials or the exchange response. */ }
            }
            // Leave the code-bearing URL before rendering the completion page.
            $request->session()->flash('qookie_connect_result', $ok);
            return redirect()->route('qookie-statamic.cp.complete')
                ->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
        } finally { $lock->release(); }
    }

    public function complete(Request $request)
    {
        $this->authorizePilot();
        return response()->view('qookie-statamic::connection-complete', [
            'ok' => $request->session()->get('qookie_connect_result') === true,
        ])->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function disconnect(Connection $connection)
    {
        $this->authorizePilot();
        $data = $connection->read();
        if ($data) {
            try {
                $response = Http::acceptJson()->withoutRedirecting()->timeout(10)
                    ->withToken($data['server_secret'])->delete($this->apiBase().'/api/v2/installation');
                if (!$response->successful() && $response->status() !== 401) return back()->withError(__('qookie-statamic::connection.failed'));
            } catch (\Throwable) { return back()->withError(__('qookie-statamic::connection.failed')); }
            $connection->forget();
        }
        return back()->withSuccess(__('qookie-statamic::connection.disconnected'));
    }
}
