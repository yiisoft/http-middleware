<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests;

use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequestFactory;
use HttpSoft\Message\StreamFactory;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\ContentLengthMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use Yiisoft\HttpMiddleware\Tests\Support\StreamStub;
use Yiisoft\HttpMiddleware\Tests\Support\StrictResponse;

use function PHPUnit\Framework\assertSame;

final class ContentLengthMiddlewareTest extends TestCase
{
    public function testBase(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new Response(404),
        );
        $middleware = new ContentLengthMiddleware();

        $response = $middleware->process($request, $handler);

        assertSame($request, $handler->getLastRequest());
        assertSame(404, $response->getStatusCode());
    }

    public function testRemoveOnTransferEncoding(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new Response(headers: ['Content-Length' => '123', 'Transfer-Encoding' => 'chunked']),
        );
        $middleware = new ContentLengthMiddleware();

        $response = $middleware->process($request, $handler);

        assertSame(['Transfer-Encoding' => ['chunked']], $response->getHeaders());
    }

    public function testDoNotChangeExistedContentLength(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new Response(
                headers: ['Content-Length' => '500'],
                body: (new StreamFactory())->createStream('Hello World'),
            ),
        );
        $middleware = new ContentLengthMiddleware();

        $response = $middleware->process($request, $handler);

        assertSame(['Content-Length' => ['500']], $response->getHeaders());
    }

    public function testAddContentLength(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new StrictResponse(
                body: (new StreamFactory())->createStream('Hello World'),
            ),
        );
        $middleware = new ContentLengthMiddleware();

        $response = $middleware->process($request, $handler);

        assertSame(['Content-Length' => ['11']], $response->getHeaders());
    }

    public function testDisabledAdd(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new Response(
                body: (new StreamFactory())->createStream('Hello World'),
            ),
        );
        $middleware = new ContentLengthMiddleware(add: false);

        $response = $middleware->process($request, $handler);

        assertSame([], $response->getHeaders());
    }

    #[TestWith([100])]
    #[TestWith([101])]
    #[TestWith([102])]
    #[TestWith([204])]
    #[TestWith([205])]
    #[TestWith([304])]
    public function testDoNotAddOnStatusCodeDefaults(int $statusCode): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new Response(
                $statusCode,
                body: (new StreamFactory())->createStream('Hello World'),
            ),
        );
        $middleware = new ContentLengthMiddleware();

        $response = $middleware->process($request, $handler);

        assertSame([], $response->getHeaders());
    }

    public function testDoNotAddOnZeroLength(): void
    {
        $body = (new StreamFactory())->createStream();
        assertSame(0, $body->getSize());

        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new Response(body: $body),
        );
        $middleware = new ContentLengthMiddleware();

        $response = $middleware->process($request, $handler);

        assertSame([], $response->getHeaders());
    }

    public function testNotReadableStream(): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        $handler = new FakeRequestHandler(
            new StrictResponse(
                body: new StreamStub(readable: false),
            ),
        );
        $middleware = new ContentLengthMiddleware();

        $response = $middleware->process($request, $handler);

        assertSame([], $response->getHeaders());
    }
}
