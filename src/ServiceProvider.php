<?php

namespace Qodli\QookieStatamic;

use Illuminate\Support\Facades\Route;
use Qodli\QookieStatamic\Http\Controllers\SettingsController;
use Qodli\QookieStatamic\Tags\Qookieqloud;
use Statamic\Facades\CP\Nav;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        Qookieqloud::class,
    ];

    protected $routes = [
        'web' => __DIR__ . '/../routes/web.php',
    ];

    protected $publishables = [
        __DIR__ . '/../config/qookie-statamic.php' => 'config/qookie-statamic.php',
    ];

    public function bootAddon(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/qookie-statamic.php', 'qookie-statamic');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'qookie-statamic');

        $this->bootControlPanel();
    }

    protected function bootControlPanel(): void
    {
        Nav::extend(function ($nav): void {
            $nav->content('QookieQloud')
                ->section('Tools')
                ->route('qookie-statamic.cp.settings')
                ->icon('shield-check');
        });

        Route::middleware(['statamic.cp.authenticated'])
            ->prefix(config('statamic.cp.route', 'cp'))
            ->name('qookie-statamic.cp.')
            ->group(function (): void {
                Route::get('/qookieqloud', [SettingsController::class, 'show'])->name('settings');
            });
    }
}
