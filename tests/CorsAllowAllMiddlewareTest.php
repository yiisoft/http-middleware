<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests;

use HttpSoft\Message\Response;
use HttpSoft\Message\ResponseFactory;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\CorsAllowAllMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class CorsAllowAllMiddlewareTest extends TestCase
{
    public function testBase(): void
    {
        $request = new ServerRequest();
        $requestHandler = new FakeRequestHandler(new Response());
        $middleware = new CorsAllowAllMiddleware();

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame(
            [
                'Vary' => ['Origin'],
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Allow-Methods' => ['GET,OPTIONS,HEAD,POST,PUT,PATCH,DELETE'],
                'Access-Control-Max-Age' => ['86400'],
                'Access-Control-Allow-Headers' => ['*'],
                'Access-Control-Expose-Headers' => ['*'],
            ],
            $response->getHeaders(),
        );
    }

    public function testCredentialedRequest(): void
    {
        $request = (new ServerRequest())
            ->withHeader('Origin', 'https://example.com');
        $requestHandler = new FakeRequestHandler(
            (new Response())
                ->withHeader('X-Request-Id', '42')
                ->withHeader('Set-Cookie', 'token=secret'),
        );

        $response = (new CorsAllowAllMiddleware())->process($request, $requestHandler);

        assertSame('https://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
        assertSame('true', $response->getHeaderLine('Access-Control-Allow-Credentials'));
        assertSame('X-Request-Id', $response->getHeaderLine('Access-Control-Expose-Headers'));
    }

    public function testRequestWithFactoryIsHandled(): void
    {
        $request = new ServerRequest();
        $requestHandler = new FakeRequestHandler();

        (new CorsAllowAllMiddleware(new ResponseFactory()))->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
    }

    public function testOptionsWithoutRequestedMethodIsHandled(): void
    {
        $request = (new ServerRequest())->withMethod('OPTIONS');
        $requestHandler = new FakeRequestHandler();

        (new CorsAllowAllMiddleware(new ResponseFactory()))->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
    }

    public function testPreflight(): void
    {
        $request = (new ServerRequest())
            ->withMethod('OPTIONS')
            ->withHeader('Origin', 'https://example.com')
            ->withHeader('Access-Control-Request-Method', 'POST')
            ->withHeader('Access-Control-Request-Headers', 'Content-Type, Authorization');
        $requestHandler = new FakeRequestHandler();

        $response = (new CorsAllowAllMiddleware(new ResponseFactory()))->process($request, $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(204, $response->getStatusCode());
        assertSame('https://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
        assertSame('Content-Type, Authorization', $response->getHeaderLine('Access-Control-Allow-Headers'));
        assertSame('true', $response->getHeaderLine('Access-Control-Allow-Credentials'));
    }
}
