<?php

namespace Qodli\QookieStatamic\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ScriptController extends Controller
{
    public function __invoke(Request $request): string
    {
        if (! config('qookie-statamic.enabled')) {
            return '';
        }

        if (! config('qookie-statamic.load_for_authenticated') && Auth::check()) {
            return '';
        }

        $loaderUrl = e(config('qookie-statamic.loader_url'));

        return <<<HTML
<script async src="{$loaderUrl}"></script>
HTML;
    }
}
