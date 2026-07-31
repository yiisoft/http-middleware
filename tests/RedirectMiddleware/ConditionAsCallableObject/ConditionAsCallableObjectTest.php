<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\RedirectMiddleware\ConditionAsCallableObject;

use HttpSoft\Message\Response;
use HttpSoft\Message\ResponseFactory;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\RedirectMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class ConditionAsCallableObjectTest extends TestCase
{
    public function testConditionTrue(): void
    {
        $middleware = new RedirectMiddleware(
            new ResponseFactory(),
            'https://example.com',
            301,
            new ConditionStub(true),
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
            new ConditionStub(false),
        );

        $handlerResponse = new Response();
        $requestHandler = new FakeRequestHandler($handlerResponse);
        $request = new ServerRequest();

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame($handlerResponse, $response);
    }
}
