<?php

namespace Qodli\QookieStatamic\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsentLoader
{
    private const LOADER_URL = 'https://cf-cdn.qookieqloud.com/consentLoader.js';

    public function __construct(private Settings $settings)
    {
    }

    public function shouldLoad(?Request $request = null): bool
    {
        if (! $this->settings->enabled()) {
            return false;
        }

        if ($request && $this->isControlPanelRequest($request)) {
            return false;
        }

        if (! $this->settings->loadForAuthenticated() && Auth::check()) {
            return false;
        }

        return true;
    }

    public function render(): string
    {
        if (config('qookie-statamic.v2_enabled')) {
            $connection = app(Connection::class)->publicData();
            if (!$connection) return '';
            $loaderUrl = e(config('qookie-statamic.v2_loader_url'));
            $siteKey = e($connection['site_key']);
            return '<script async src="'.$loaderUrl.'" data-site-key="'.$siteKey.'"></script>';
        }
        $loaderUrl = e(self::LOADER_URL);

        return <<<HTML
<script async src="{$loaderUrl}"></script>
HTML;
    }

    private function isControlPanelRequest(Request $request): bool
    {
        $cpRoute = trim(config('statamic.cp.route', 'cp'), '/');

        return $request->is($cpRoute) || $request->is($cpRoute . '/*');
    }
}
