<?php

namespace Qodli\QookieStatamic\Http\Controllers;

use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    public function show()
    {
        return view('qookie-statamic::settings', [
            'enabled' => config('qookie-statamic.enabled'),
            'loaderUrl' => config('qookie-statamic.loader_url'),
            'appUrl' => config('qookie-statamic.app_url'),
            'loadForAuthenticated' => config('qookie-statamic.load_for_authenticated'),
        ]);
    }
}
