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
        $validated = request()->validate([
            'enabled' => ['nullable', 'boolean'],
            'load_for_authenticated' => ['nullable', 'boolean'],
        ]);

        $settings->save([
            'enabled' => (bool) ($validated['enabled'] ?? false),
            'load_for_authenticated' => (bool) ($validated['load_for_authenticated'] ?? false),
        ]);

        return back()->withSuccess('QookieQloud settings saved.');
    }

    public function verify(QookieQloudApi $api)
    {
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
