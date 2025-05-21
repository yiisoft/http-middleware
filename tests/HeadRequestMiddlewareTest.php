<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests;

use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequest;
use HttpSoft\Message\StreamFactory;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HeadRequestMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertSame;

final class HeadRequestMiddlewareTest extends TestCase
{
    #[TestWith([true, 'GET'])]
    #[TestWith([true, 'POST'])]
    #[TestWith([true, 'PUT'])]
    #[TestWith([true, 'DELETE'])]
    #[TestWith([true, 'PATCH'])]
    #[TestWith([true, 'OPTIONS'])]
    #[TestWith([false, 'HEAD'])]
    public function testBase(bool $existsBody, string $method): void
    {
        $streamFactory = new StreamFactory();
        $request = new ServerRequest(method: $method);
        $requestHandler = new FakeRequestHandler(
            new Response(body: $streamFactory->createStream('test')),
        );
        $middleware = new HeadRequestMiddleware($streamFactory);

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame($existsBody ? 'test' : '', (string) $response->getBody());
    }
}
