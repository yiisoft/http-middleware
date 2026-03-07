<?php

declare(strict_types=1);

use HttpSoft\Message\Response;
use HttpSoft\Message\ResponseFactory;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\RedirectMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class RedirectMiddlewareTest extends TestCase
{
    public function testBase(): void
    {
        $middleware = new RedirectMiddleware(new ResponseFactory(), 'https://example.com');
        $requestHandler = new FakeRequestHandler();

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(301, $response->getStatusCode());
        assertSame('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testCustomStatusCode(): void
    {
        $middleware = new RedirectMiddleware(new ResponseFactory(), 'https://example.com/new', 302);
        $requestHandler = new FakeRequestHandler();

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(302, $response->getStatusCode());
        assertSame('https://example.com/new', $response->getHeaderLine('Location'));
    }

    public function testPermanent(): void
    {
        $middleware = RedirectMiddleware::permanent(new ResponseFactory(), 'https://example.com');

        $response = $middleware->process(new ServerRequest(), new FakeRequestHandler());

        assertSame(301, $response->getStatusCode());
        assertSame('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testFound(): void
    {
        $middleware = RedirectMiddleware::found(new ResponseFactory(), 'https://example.com');

        $response = $middleware->process(new ServerRequest(), new FakeRequestHandler());

        assertSame(302, $response->getStatusCode());
        assertSame('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testSeeOther(): void
    {
        $middleware = RedirectMiddleware::seeOther(new ResponseFactory(), 'https://example.com');

        $response = $middleware->process(new ServerRequest(), new FakeRequestHandler());

        assertSame(303, $response->getStatusCode());
        assertSame('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testTemporary(): void
    {
        $middleware = RedirectMiddleware::temporary(new ResponseFactory(), 'https://example.com');

        $response = $middleware->process(new ServerRequest(), new FakeRequestHandler());

        assertSame(307, $response->getStatusCode());
        assertSame('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testConditionTrue(): void
    {
        $middleware = new RedirectMiddleware(
            new ResponseFactory(),
            'https://example.com',
            301,
            static fn() => true,
        );
        $requestHandler = new FakeRequestHandler();

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertNull($requestHandler->getLastRequest());
        assertSame(301, $response->getStatusCode());
        assertSame('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testConditionFalse(): void
    {
        $middleware = new RedirectMiddleware(
            new ResponseFactory(),
            'https://example.com',
            301,
            static fn() => false,
        );

        $handlerResponse = new Response();
        $requestHandler = new FakeRequestHandler($handlerResponse);
        $request = new ServerRequest();

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame($handlerResponse, $response);
    }

    public function testConditionReceivesRequest(): void
    {
        $receivedRequest = null;
        $request = new ServerRequest();

        $middleware = new RedirectMiddleware(
            new ResponseFactory(),
            'https://example.com',
            301,
            static function ($request) use (&$receivedRequest) {
                $receivedRequest = $request;
                return true;
            },
        );

        $middleware->process($request, new FakeRequestHandler());

        assertSame($request, $receivedRequest);
    }
}
