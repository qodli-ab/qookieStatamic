<?php

namespace Qodli\QookieStatamic\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PrivateConnection
{
    public function handle(Request $request, Closure $next)
    {
        if (!config('qookie-statamic.v2_enabled')) return $next($request);
        // The outgoing exchange contains installation credentials. Keep debug collectors off.
        if (app()->bound('debugbar')) app('debugbar')->disable();
        if (class_exists(\Laravel\Telescope\Telescope::class)) \Laravel\Telescope\Telescope::stopRecording();
        $response = $next($request);
        $response->headers->set('Cache-Control', 'private, no-store');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        return $response;
    }
}
