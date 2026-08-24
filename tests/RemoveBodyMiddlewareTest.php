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
    #[TestWith([false, 103])]
    #[TestWith([false, 204])]
    #[TestWith([false, 205])]
    #[TestWith([false, 304])]
    public function testBase(bool $expectBody, int $statusCode): void
    {
        $streamFactory = new StreamFactory();
        $requestHandler = new FakeRequestHandler(
            new Response(
                statusCode: $statusCode,
                body: (new StreamFactory())->createStream('test'),
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
                body: (new StreamFactory())->createStream('test'),
            ),
        );
        $middleware = new RemoveBodyMiddleware($streamFactory, [100, 404]);

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertSame($expectBody ? 'test' : '', (string) $response->getBody());
    }

    #[TestWith([true, 100])]
    #[TestWith([true, 101])]
    #[TestWith([true, 102])]
    #[TestWith([true, 103])]
    #[TestWith([true, 204])]
    #[TestWith([true, 205])]
    #[TestWith([false, 304])]
    public function testHeadersAreRemovedExceptForKeptStatusCodes(bool $expectHeadersRemoved, int $statusCode): void
    {
        $streamFactory = new StreamFactory();
        $requestHandler = new FakeRequestHandler(
            (new Response(
                statusCode: $statusCode,
                body: (new StreamFactory())->createStream('test'),
            ))
                ->withHeader('Content-Length', '4')
                ->withHeader('Transfer-Encoding', 'chunked'),
        );
        $middleware = new RemoveBodyMiddleware($streamFactory);

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertSame(!$expectHeadersRemoved, $response->hasHeader('Content-Length'));
        assertSame(!$expectHeadersRemoved, $response->hasHeader('Transfer-Encoding'));
    }

    public function testCustomKeepHeadersOnStatusCode(): void
    {
        $streamFactory = new StreamFactory();
        $requestHandler = new FakeRequestHandler(
            (new Response(
                statusCode: 204,
                body: (new StreamFactory())->createStream('test'),
            ))
                ->withHeader('Content-Length', '4')
                ->withHeader('Transfer-Encoding', 'chunked'),
        );
        $middleware = new RemoveBodyMiddleware(
            $streamFactory,
            keepHeadersOnStatusCode: [204],
        );

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertSame(true, $response->hasHeader('Content-Length'));
        assertSame(true, $response->hasHeader('Transfer-Encoding'));
    }

    public function testCustomRemovedHeaders(): void
    {
        $streamFactory = new StreamFactory();
        $requestHandler = new FakeRequestHandler(
            (new Response(
                statusCode: 204,
                body: (new StreamFactory())->createStream('test'),
            ))
                ->withHeader('Content-Length', '4')
                ->withHeader('Transfer-Encoding', 'chunked'),
        );
        $middleware = new RemoveBodyMiddleware(
            $streamFactory,
            removedHeaders: ['Content-Length'],
        );

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertSame(false, $response->hasHeader('Content-Length'));
        assertSame(true, $response->hasHeader('Transfer-Encoding'));
    }
}
