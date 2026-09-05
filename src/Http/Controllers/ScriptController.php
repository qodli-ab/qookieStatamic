<?php

namespace Qodli\QookieStatamic\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Qodli\QookieStatamic\Support\ConsentLoader;

class ScriptController extends Controller
{
    public function __construct(private ConsentLoader $loader)
    {
    }

    public function __invoke(Request $request): string
    {
        return $this->loader->shouldLoad($request) ? $this->loader->render() : '';
    }
}
