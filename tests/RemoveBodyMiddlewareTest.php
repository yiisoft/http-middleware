<?php

declare(strict_types=1);

use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequest;
use HttpSoft\Message\StreamFactory;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\RemoveBodyMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertSame;

final class RemoveBodyMiddlewareTest extends TestCase
{
    #[TestWith([true, 200])]
    #[TestWith([true, 500])]
    #[TestWith([false, 100])]
    #[TestWith([false, 101])]
    #[TestWith([false, 102])]
    #[TestWith([false, 204])]
    #[TestWith([false, 205])]
    #[TestWith([false, 304])]
    public function testBase(bool $expectBody, int $statusCode): void
    {
        $streamFactory = new StreamFactory();
        $requestHandler = new FakeRequestHandler(
            new Response(
                statusCode: $statusCode,
                body: (new StreamFactory())->createStream('test')
            ),
        );
        $middleware = new RemoveBodyMiddleware($streamFactory);

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertSame($expectBody ? 'test' : '', (string) $response->getBody());
    }

    #[TestWith([true, 200])]
    #[TestWith([true, 304])]
    #[TestWith([false, 100])]
    #[TestWith([false, 404])]
    public function testCustomStatus(bool $expectBody, int $statusCode): void
    {
        $streamFactory = new StreamFactory();
        $requestHandler = new FakeRequestHandler(
            new Response(
                statusCode: $statusCode,
                body: (new StreamFactory())->createStream('test')
            ),
        );
        $middleware = new RemoveBodyMiddleware($streamFactory, [100, 404]);

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertSame($expectBody ? 'test' : '', (string) $response->getBody());
    }
}
