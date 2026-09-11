<?php

namespace Qodli\QookieStatamic\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Qodli\QookieStatamic\Support\ConsentLoader;
use Symfony\Component\HttpFoundation\Response;

class InjectQookieqloud
{
    public function __construct(private ConsentLoader $loader)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldInject($request, $response)) {
            return $response;
        }

        $content = $response->getContent();
        $script = $this->loader->render();

        if (str_contains($content, $script) || ! str_contains(strtolower($content), '</body>')) {
            return $response;
        }

        $content = preg_replace('/<\/body>/i', $script . PHP_EOL . '</body>', $content, 1);
        $response->setContent($content);
        $response->headers->remove('Content-Length');

        return $response;
    }

    private function shouldInject(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->expectsJson() || $request->header('X-Inertia')) {
            return false;
        }

        if (! $response->isSuccessful() || $response->isRedirection()) {
            return false;
        }

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }

        return $this->loader->shouldLoad($request);
    }
}
