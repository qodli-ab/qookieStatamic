<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Qodli\QookieStatamic\Http\Controllers\ConnectController;
use Qodli\QookieStatamic\Support\Connection;
use Qodli\QookieStatamic\Support\ConsentLoader;
use Statamic\Facades\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class QookieV2ConnectionTest extends TestCase
{
    private string $connectionPath;
    protected function setUp(): void
    {
        parent::setUp();
        $this->connectionPath = sys_get_temp_dir().'/qq-plugin-test-'.bin2hex(random_bytes(8)).'/connection.enc';
        config(['qookie-statamic.connection_path' => $this->connectionPath, 'qookie-statamic.v2_enabled' => true,
            'qookie-statamic.app_url' => 'https://qq.example', 'app.key' => 'base64:'.base64_encode(str_repeat('x',32))]);
        Http::preventStrayRequests();
        $user = \Mockery::mock(\Statamic\Contracts\Auth\User::class);
        $user->shouldReceive('isSuper')->andReturn(true);
        $user->shouldReceive('id')->andReturn('admin');
        User::shouldReceive('current')->andReturn($user);
    }
    protected function tearDown(): void
    {
        if (is_file($this->connectionPath)) unlink($this->connectionPath);
        if (is_dir(dirname($this->connectionPath))) rmdir(dirname($this->connectionPath));
        parent::tearDown();
    }
    private function credentials(): array
    {
        return ['installation_id' => 42, 'domain' => 'qookieqloud.com', 'site_key' => 'qq_pk_'.str_repeat('a',64),
            'server_secret' => 'qq_sec_'.str_repeat('b',64), 'scopes' => ['installation:read']];
    }
    private function request(array $query = []): Request
    {
        $request = Request::create('http://127.0.0.1/cp/qookieqloud/callback', 'GET', $query);
        $request->setLaravelSession(app('session.store'));
        return $request;
    }
    public function test_connected_dashboard_uses_private_v2_api_and_handles_missing_stats(): void
    {
        app(Connection::class)->save($this->credentials());
        Http::fake([
            'https://qq.example/api/v2/installation' => Http::sequence()->push(['status'=>'connected'])->push([], 404),
            'https://qq.example/api/v2/installation/dashboard' => Http::response(['domain'=>'qookieqloud.com', 'consents_today'=>12]),
        ]);
        $controller = app(\Qodli\QookieStatamic\Http\Controllers\SettingsController::class);
        $view = app()->call([$controller, 'show']);
        $this->assertSame('qookie-statamic::dashboard', $view->name());
        $this->assertSame(12, $view->getData()['stats']['consents_today']);
        $this->assertArrayNotHasKey('server_secret', $view->getData()['connection']);
        Http::assertSent(fn ($request) => $request->url() === 'https://qq.example/api/v2/installation/dashboard'
            && $request->hasHeader('Authorization', 'Bearer '.$this->credentials()['server_secret']));
        $view = app()->call([$controller, 'show']);
        $this->assertSame('qookie-statamic::dashboard', $view->name());
        $this->assertSame([], $view->getData()['stats']);
    }

    public function test_unconnected_dashboard_shows_guide_without_api_calls(): void
    {
        $view = app()->call([app(\Qodli\QookieStatamic\Http\Controllers\SettingsController::class), 'show']);
        $this->assertSame('qookie-statamic::connection', $view->name());
        Http::assertNothingSent();
    }

    public function test_custom_callback_route_keeps_production_destination(): void
    {
        \Illuminate\Support\Facades\URL::forceRootUrl('https://cms.customer.example:8443');
        \Illuminate\Support\Facades\URL::forceScheme('https');
        $routes = new \Illuminate\Routing\RouteCollection;
        $routes->add((new \Illuminate\Routing\Route('GET', 'manage-site/qookieqloud/callback', fn () => ''))->name('qookie-statamic.cp.callback'));
        \Illuminate\Support\Facades\URL::setRoutes($routes);
        config(['qookie-statamic.app_url' => 'https://app.qookieqloud.com']);
        $result = app(ConnectController::class)->start($this->request());
        $this->assertSame('app.qookieqloud.com', parse_url($result->getTargetUrl(), PHP_URL_HOST));
        parse_str(parse_url($result->getTargetUrl(), PHP_URL_QUERY), $query);
        $this->assertSame('https://cms.customer.example:8443/manage-site/qookieqloud/callback', $query['redirect_uri']);
    }

    public function test_credentials_are_encrypted_and_only_public_key_is_in_v2_tag(): void
    {
        $connection = app(Connection::class);
        $connection->save($this->credentials());
        $this->assertSame($this->credentials(), $connection->read());
        $this->assertStringNotContainsString('qq_sec_', file_get_contents($this->connectionPath));
        $this->assertSame(0600, fileperms($this->connectionPath) & 0777);
        $html = app(ConsentLoader::class)->render();
        $this->assertStringContainsString('/v2/consentLoader.js', $html);
        $this->assertStringContainsString($this->credentials()['site_key'], $html);
        $this->assertStringNotContainsString('qq_sec_', $html);
        $this->assertArrayNotHasKey('server_secret', $connection->publicData());
    }
    public function test_v2_without_connection_does_not_fall_back_but_v1_is_unchanged(): void
    {
        $this->assertSame('', app(ConsentLoader::class)->render());
        config(['qookie-statamic.v2_enabled' => false]);
        $this->assertSame('<script async src="https://cf-cdn.qookieqloud.com/consentLoader.js"></script>', app(ConsentLoader::class)->render());
    }
    public function test_pkce_exchange_is_outgoing_and_callback_leaves_code_url(): void
    {
        $controller = app(ConnectController::class);
        $request = $this->request();
        $response = $controller->start($request);
        parse_str(parse_url($response->getTargetUrl(), PHP_URL_QUERY), $query);
        $attempt = $request->session()->get('qookie_connect');
        $this->assertSame('S256', $query['code_challenge_method']);
        $this->assertSame(rtrim(strtr(base64_encode(hash('sha256',$attempt['verifier'],true)),'+/','-_'),'='), $query['code_challenge']);
        $this->assertArrayNotHasKey('code_verifier', $query);
        Http::fake(['https://qq.example/api/v2/pairing/exchange' => Http::response($this->credentials())]);
        $callback = $this->request(['state' => $attempt['state'], 'auth_code' => str_repeat('c',64)]);
        $result = $controller->callback($callback, app(Connection::class));
        $this->assertStringEndsWith('/cp/qookieqloud/complete', $result->getTargetUrl());
        $this->assertNull($callback->session()->get('qookie_connect'));
        $this->assertTrue($callback->session()->get('qookie_connect_result'));
        $this->assertSame($this->credentials(), app(Connection::class)->read());
        Http::assertSent(fn ($r) => $r['code_verifier'] === $attempt['verifier'] && $r['redirect_uri'] === $attempt['callback']);
        try { $controller->callback($callback, app(Connection::class)); $this->fail('Replay accepted'); }
        catch (HttpException $e) { $this->assertSame(403,$e->getStatusCode()); }
        Http::assertSentCount(1);
    }
    public function test_wrong_state_never_calls_backend(): void
    {
        $controller = app(ConnectController::class);
        $controller->start($this->request());
        Http::fake();
        try { $controller->callback($this->request(['state'=>'forged']), app(Connection::class)); $this->fail('Wrong state accepted'); }
        catch (HttpException $e) { $this->assertSame(403,$e->getStatusCode()); }
        Http::assertNothingSent();
    }
    public function test_failed_exchange_preserves_existing_connection(): void
    {
        $connection = app(Connection::class);
        $connection->save($this->credentials());
        $controller = app(ConnectController::class);
        $request = $this->request();
        $controller->start($request);
        $attempt = $request->session()->get('qookie_connect');
        Http::fake(['*' => Http::response([], 401)]);
        $callback = $this->request(['state'=>$attempt['state'],'auth_code'=>str_repeat('d',64)]);
        $controller->callback($callback,$connection);
        $this->assertFalse($callback->session()->get('qookie_connect_result'));
        $this->assertSame($this->credentials(),$connection->read());
    }
}
