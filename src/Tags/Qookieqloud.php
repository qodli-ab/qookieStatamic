<?php

namespace Qodli\QookieStatamic\Tags;

use Statamic\Tags\Tags;

class Qookieqloud extends Tags
{
    public function index(): string
    {
        return app(\Qodli\QookieStatamic\Http\Controllers\ScriptController::class)(request());
    }
}
