<?php

namespace Qodli\QookieStatamic;

use Illuminate\Support\Facades\Route;
use Qodli\QookieStatamic\Http\Middleware\InjectQookieqloud;
use Qodli\QookieStatamic\Http\Controllers\SettingsController;
use Qodli\QookieStatamic\Tags\Qookieqloud;
use Statamic\Facades\CP\Nav;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $stylesheets = [
        __DIR__ . '/../resources/css/cp.css',
    ];

    protected $tags = [
        Qookieqloud::class,
    ];

    protected $routes = [
        'web' => __DIR__ . '/../routes/web.php',
    ];

    public function bootAddon(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/qookie-statamic.php', 'qookie-statamic');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'qookie-statamic');
        $this->app['router']->pushMiddlewareToGroup('web', InjectQookieqloud::class);

        $this->bootControlPanel();
    }

    protected function bootControlPanel(): void
    {
        Nav::extend(function ($nav): void {
            $nav->content('QookieQloud')
                ->section('Tools')
                ->url('qookieqloud')
                ->icon($this->navIcon());
        });

        Route::middleware(['statamic.cp', 'statamic.cp.authenticated'])
            ->prefix(config('statamic.cp.route', 'cp'))
            ->name('qookie-statamic.cp.')
            ->group(function (): void {
                Route::get('/qookieqloud', [SettingsController::class, 'show'])->name('settings');
                Route::post('/qookieqloud', [SettingsController::class, 'update'])->name('update');
                Route::post('/qookieqloud/verify', [SettingsController::class, 'verify'])->name('verify');
            });
    }

    protected function navIcon(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 141.9 168.1" aria-hidden="true">
    <path fill="currentColor" d="M0,93.9v-18.3c0-8.3,1-15.5,2.9-21.8,1.9-6.2,4.7-11.5,8.3-15.6,3.6-4.2,7.9-7.3,12.9-9.4s10.5-3.2,16.6-3.2,11.7,1.1,16.6,3.2c5,2.1,9.3,5.2,12.9,9.4s6.4,9.4,8.4,15.6,3,13.5,3,21.8v18.3c0,8.3-1,15.5-2.9,21.8-1.9,6.2-4.8,11.4-8.4,15.6-3.6,4.2-8,7.3-12.9,9.4s-10.5,3.2-16.6,3.2-11.7-1.1-16.7-3.2c-5-2.1-9.3-5.3-13-9.4s-6.4-9.4-8.4-15.6c-1.7-6.3-2.7-13.5-2.7-21.8ZM20.8,74.8v18.5c0,5.6.4,10.5,1.3,14.6.8,4.1,2.1,7.4,3.8,9.9,1.7,2.6,3.8,4.4,6.2,5.7,2.5,1.2,5.3,1.8,8.5,1.8s6-.6,8.4-1.8c2.5-1.2,4.5-3.1,6.2-5.7s2.9-5.9,3.8-9.9,1.3-8.9,1.3-14.6v-18.5c0-5.7-.4-10.6-1.3-14.6-.9-4.1-2.2-7.4-3.8-10-1.7-2.6-3.8-4.5-6.2-5.7s-5.3-1.8-8.5-1.8-6,.6-8.4,1.8c-2.5,1.2-4.5,3.1-6.2,5.7s-2.9,5.9-3.8,10c-.8,4.1-1.2,8.9-1.2,14.6h0ZM25.1,120.3l40.8,34.8-10.6,13-40.7-33.4,10.5-14.4h0Z"/>
    <path fill="currentColor" d="M141.9,74.2v18.3c0,8.3-1,15.5-2.9,21.8s-4.7,11.5-8.3,15.6c-3.6,4.2-7.9,7.3-12.9,9.4s-10.6,3.2-16.6,3.2-11.7-1.1-16.7-3.2c-5-2.1-9.3-5.2-12.9-9.4s-6.4-9.4-8.4-15.6-3-13.5-3-21.8v-18.3c0-8.3,1-15.5,2.9-21.8,2-6.2,4.7-11.4,8.4-15.6,3.6-4.2,7.9-7.3,12.9-9.5s10.5-3.2,16.6-3.2,11.7,1.1,16.7,3.2,9.3,5.3,13,9.5,6.4,9.4,8.4,15.6c1.9,6.2,2.9,13.5,2.9,21.8h0ZM121.2,93.3v-18.5c0-5.6-.4-10.5-1.3-14.5-.8-4.1-2.1-7.4-3.8-9.9s-3.8-4.4-6.2-5.7c-2.4-1.2-5.3-1.8-8.5-1.8s-6,.6-8.4,1.8-4.5,3.1-6.2,5.7c-1.7,2.6-2.9,5.9-3.8,9.9s-1.3,8.9-1.3,14.5v18.5c0,5.7.4,10.6,1.3,14.6.9,4.1,2.2,7.4,3.8,10,1.7,2.6,3.8,4.5,6.2,5.7,2.4,1.2,5.3,1.8,8.5,1.8s6-.6,8.4-1.8,4.5-3.1,6.2-5.7,2.9-5.9,3.8-10c.8-4.1,1.2-8.9,1.2-14.6h.1ZM116.8,47.8L76,13,86.6,0l40.7,33.4-10.4,14.4h0Z"/>
    <path fill="#5cbf8b" d="M27.75,82.2c0-3.45,1.2-6.3,3.3-8.7s5.25-3.45,9.3-3.45,7.05,1.2,9.3,3.45,3.3,5.25,3.3,8.7-1.2,6.15-3.3,8.4-5.25,3.45-9.3,3.45-7.05-1.2-9.3-3.45-3.3-5.1-3.3-8.4Z"/>
    <path fill="#5cbf8b" d="M88.05,82.2c0-3.45,1.2-6.3,3.3-8.7s5.4-3.45,9.3-3.45,7.05,1.2,9.3,3.45,3.3,5.25,3.3,8.7-1.2,6.15-3.3,8.4-5.4,3.45-9.3,3.45-7.05-1.2-9.3-3.45-3.3-5.1-3.3-8.4Z"/>
</svg>
SVG;
    }
}
