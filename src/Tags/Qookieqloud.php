<?php

namespace Qodli\QookieStatamic\Tags;

use Qodli\QookieStatamic\Support\ConsentLoader;
use Statamic\Tags\Tags;

class Qookieqloud extends Tags
{
    public function index(): string
    {
        $loader = app(ConsentLoader::class);

        return $loader->shouldLoad(request()) ? $loader->render() : '';
    }
}
