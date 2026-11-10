<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Qodli\QookieStatamic\Http\Middleware\InjectQookieqloud;
use Qodli\QookieStatamic\Support\ConsentLoader;
use Symfony\Component\HttpFoundation\Response;

class InjectQookieqloudTest extends TestCase
{
    private const SCRIPT = '<script async src="https://cf-cdn.qookieqloud.com/v2/consentLoader.js" data-site-key="qq_pk_test"></script>';

    private function inject(string $html, string $script = self::SCRIPT, bool $enabled = true, string $method = 'GET', int $status = 200, string $type = 'text/html'): Response
    {
        $loader = $this->createStub(ConsentLoader::class);
        $loader->method('shouldLoad')->willReturn($enabled);
        $loader->method('render')->willReturn($script);
        $response = new Response($html, $status, ['Content-Type' => $type, 'Content-Length' => (string) strlen($html)]);

        return (new InjectQookieqloud($loader))->handle(Request::create('/', $method), fn () => $response);
    }

    public function test_inserts_loader_at_end_of_head_before_body_without_creating_a_container(): void
    {
        $html = '<html><head><title>Test</title></head><body><main>Page</main></body></html>';
        $response = $this->inject($html);

        $this->assertSame('<html><head><title>Test</title>'.self::SCRIPT.PHP_EOL.'</head><body><main>Page</main></body></html>', $response->getContent());
        $this->assertFalse($response->headers->has('Content-Length'));
        $this->assertStringNotContainsString('id="qookie-consent"', $response->getContent());
    }

    public function test_handles_uppercase_closing_head(): void
    {
        $this->assertSame('<HEAD>'.self::SCRIPT.PHP_EOL.'</head><BODY></BODY>', $this->inject('<HEAD></HEAD><BODY></BODY>')->getContent());
    }

    public function test_does_not_fall_back_to_body_when_closing_head_is_missing(): void
    {
        foreach (['<html><body>Page</body></html>', '<head><title>Incomplete</title><body></body>', '<p>Fragment</p>'] as $html) {
            $response = $this->inject($html);
            $this->assertSame($html, $response->getContent());
            $this->assertSame((string) strlen($html), $response->headers->get('Content-Length'));
        }
    }

    public function test_preserves_existing_loader_in_head_or_body_without_duplicates(): void
    {
        foreach (['<head>'.self::SCRIPT.'</head><body></body>', '<head></head><body>'.self::SCRIPT.'</body>'] as $html) {
            $this->assertSame($html, $this->inject($html)->getContent());
        }
    }

    public function test_repeated_middleware_processing_is_idempotent(): void
    {
        $once = $this->inject('<head></head><body></body>')->getContent();
        $twice = $this->inject($once)->getContent();
        $this->assertSame($once, $twice);
        $this->assertSame(1, substr_count($twice, self::SCRIPT));
    }

    public function test_inserts_only_at_first_closing_head(): void
    {
        $this->assertSame('<head>'.self::SCRIPT.PHP_EOL.'</head></head><body></body>', $this->inject('<head></head></head><body></body>')->getContent());
    }

    public function test_empty_loader_or_disabled_banner_leaves_response_unchanged(): void
    {
        $html = '<head></head><body></body>';
        $this->assertSame($html, $this->inject($html, '')->getContent());
        $this->assertSame($html, $this->inject($html, self::SCRIPT, false)->getContent());
    }

    public function test_non_html_failed_and_non_get_responses_are_unchanged(): void
    {
        $html = '<head></head><body></body>';
        $this->assertSame($html, $this->inject($html, method: 'POST')->getContent());
        $this->assertSame($html, $this->inject($html, status: 404)->getContent());
        $this->assertSame($html, $this->inject($html, status: 302)->getContent());
        $this->assertSame($html, $this->inject($html, type: 'application/json')->getContent());
    }
}
