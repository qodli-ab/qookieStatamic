<?php

namespace Qodli\QookieStatamic\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Qodli\QookieStatamic\Services\QookieQloudApi;
use Qodli\QookieStatamic\Support\Settings;

class SettingsController extends Controller
{
    public function show(QookieQloudApi $api, Settings $settings)
    {
        if (config('qookie-statamic.v2_enabled')) {
            $user = \Statamic\Facades\User::current();
            abort_unless($user && $user->isSuper(), 403);
            $connection = app(\Qodli\QookieStatamic\Support\Connection::class);
            $private = $connection->read();
            $stats = [];
            $status = __('qookie-statamic::connection.unavailable');
            if ($private) {
                try {
                    $response = \Illuminate\Support\Facades\Http::acceptJson()->withoutRedirecting()->timeout(8)
                        ->withToken($private['server_secret'])->get(rtrim(config('qookie-statamic.app_url'), '/') . '/api/v2/installation');
                    if ($response->successful() && $response->json('status') === 'connected') {
                        $status = __('qookie-statamic::connection.available');
                        $dashboard = \Illuminate\Support\Facades\Http::acceptJson()->withoutRedirecting()->timeout(8)
                            ->withToken($private['server_secret'])->get(rtrim(config('qookie-statamic.app_url'), '/') . '/api/v2/installation/dashboard');
                        if ($dashboard->successful() && $dashboard->json('domain') === $private['domain']) $stats = $dashboard->json();
                    }
                } catch (\Throwable) {}
            }
            return view($private ? 'qookie-statamic::dashboard' : 'qookie-statamic::connection', ['stats' => $stats, 'dashboardUrl' => rtrim(config('qookie-statamic.app_url'), '/') . '/app', 'connection' => $connection->publicData(), 'logoBase64' => base64_encode(file_get_contents(__DIR__.'/../../../resources/images/qookieqloud-logo-white.png')), 'remoteStatus' => $status, 'settings' => $settings->all()]);
        }
        $domain = request()->getHost();
        $domainStatus = $api->checkDomain($domain);
        $stats = $api->stats($domain);
        $values = $settings->all();

        return view('qookie-statamic::settings', [
            'enabled' => $values['enabled'],
            'dashboardUrl' => $api->dashboardUrl(),
            'domain' => $domain,
            'domainStatus' => $domainStatus,
            'loadForAuthenticated' => $values['load_for_authenticated'],
            'environment' => Str::headline(app()->environment()),
            'stats' => $stats,
        ]);
    }

    public function update(Settings $settings)
    {
        if (config('qookie-statamic.v2_enabled')) {
            $user = \Statamic\Facades\User::current();
            abort_unless($user && $user->isSuper(), 403);
        }
        $validated = request()->validate([
            'enabled' => ['nullable', 'boolean'],
            'load_for_authenticated' => ['nullable', 'boolean'],
        ]);

        $settings->save([
            'enabled' => (bool) ($validated['enabled'] ?? false),
            'load_for_authenticated' => (bool) ($validated['load_for_authenticated'] ?? false),
        ]);

        return back()->withSuccess(__('qookie-statamic::connection.saved'));
    }

    public function verify(QookieQloudApi $api)
    {
        abort_if(config('qookie-statamic.v2_enabled'), 404);
        $domain = request()->getHost();
        $api->forget($domain);
        $status = $api->checkDomain($domain, true);
        $api->stats($domain, true);

        if ($status['registered']) {
            return back()->withSuccess('Domain verified in QookieQloud.');
        }

        return back()->withError($status['error'] ?: 'Domain is not registered in QookieQloud yet.');
    }
}
