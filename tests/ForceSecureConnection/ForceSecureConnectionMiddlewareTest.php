<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\ForceSecureConnection;

use HttpSoft\Message\ResponseFactory;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\ForceSecureConnection\ForceSecureConnectionMiddleware;
use Yiisoft\HttpMiddleware\ForceSecureConnection\HstsHeader;
use Yiisoft\HttpMiddleware\ForceSecureConnection\RedirectOptions;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class ForceSecureConnectionMiddlewareTest extends TestCase
{
    #[TestWith([''])]
    #[TestWith([':80'])]
    public function testHttp(string $port): void
    {
        $request = new ServerRequest(uri: "http://example.com$port/blog");
        $requestHandler = new FakeRequestHandler();
        $middleware = new ForceSecureConnectionMiddleware(new ResponseFactory());

        $response = $middleware->process($request, $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(301, $response->getStatusCode());
        assertSame(
            [
                'Location' => ['https://example.com/blog'],
                'Strict-Transport-Security' => ['max-age=31536000'],
            ],
            $response->getHeaders()
        );
    }

    public function testHttps(): void
    {
        $request = new ServerRequest(uri: 'https://example.com/blog');
        $requestHandler = new FakeRequestHandler();
        $middleware = new ForceSecureConnectionMiddleware(new ResponseFactory());

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame(
            [
                'Content-Security-Policy' => ['upgrade-insecure-requests; default-src https:'],
                'Strict-Transport-Security' => ['max-age=31536000'],
            ],
            $response->getHeaders()
        );
    }

    public function testHttpWithoutRedirection(): void
    {
        $request = new ServerRequest(uri: 'http://example.com/blog');
        $requestHandler = new FakeRequestHandler();
        $middleware = new ForceSecureConnectionMiddleware(
            new ResponseFactory(),
            redirectOptions: new RedirectOptions(false),
        );

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame(
            [
                'Content-Security-Policy' => ['upgrade-insecure-requests; default-src https:'],
                'Strict-Transport-Security' => ['max-age=31536000'],
            ],
            $response->getHeaders()
        );
    }

    public function testCustomRedirection(): void
    {
        $request = new ServerRequest(uri: 'http://example.com/blog');
        $requestHandler = new FakeRequestHandler();
        $middleware = new ForceSecureConnectionMiddleware(
            new ResponseFactory(),
            redirectOptions: new RedirectOptions(port: 8443),
        );

        $response = $middleware->process($request, $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(
            [
                'Location' => ['https://example.com:8443/blog'],
                'Strict-Transport-Security' => ['max-age=31536000'],
            ],
            $response->getHeaders()
        );
    }

    public function testCustomCspHeader(): void
    {
        $request = new ServerRequest(uri: "https://example.com");
        $middleware = new ForceSecureConnectionMiddleware(
            new ResponseFactory(),
            cspHeader: "default-src 'self'; script-src 'self' https://cdn.example.com"
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(
            [
                'Content-Security-Policy' => ["default-src 'self'; script-src 'self' https://cdn.example.com"],
                'Strict-Transport-Security' => ['max-age=31536000'],
            ],
            $response->getHeaders()
        );
    }

    public function testWithoutCspHeader(): void
    {
        $request = new ServerRequest(uri: "https://example.com");
        $middleware = new ForceSecureConnectionMiddleware(
            new ResponseFactory(),
            cspHeader: null,
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(
            [
                'Strict-Transport-Security' => ['max-age=31536000'],
            ],
            $response->getHeaders()
        );
    }

    public function testCustomHstsHeader(): void
    {
        $request = new ServerRequest(uri: 'http://example.com/blog');
        $requestHandler = new FakeRequestHandler();
        $middleware = new ForceSecureConnectionMiddleware(
            new ResponseFactory(),
            hstsHeader: new HstsHeader(500, true),
        );

        $response = $middleware->process($request, $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(
            [
                'Location' => ['https://example.com/blog'],
                'Strict-Transport-Security' => ['max-age=500; includeSubDomains'],
            ],
            $response->getHeaders()
        );
    }

    public function testWithoutHstsHeader(): void
    {
        $request = new ServerRequest(uri: 'http://example.com/blog');
        $requestHandler = new FakeRequestHandler();
        $middleware = new ForceSecureConnectionMiddleware(
            new ResponseFactory(),
            hstsHeader: null,
        );

        $response = $middleware->process($request, $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(
            [
                'Location' => ['https://example.com/blog'],
            ],
            $response->getHeaders()
        );
    }
}
