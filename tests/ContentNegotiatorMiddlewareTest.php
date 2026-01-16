<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests;

use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequestFactory;
use HttpSoft\Message\StreamFactory;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\HttpMiddleware\ContentNegotiatorMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;
use Yiisoft\HttpMiddleware\Tests\Support\MiddlewareStub;

use function PHPUnit\Framework\assertSame;

final class ContentNegotiatorMiddlewareTest extends TestCase
{
    #[TestWith(['base', null])]
    #[TestWith(['base', 'text/plain'])]
    #[TestWith(['json', 'application/json'])]
    #[TestWith(['json', 'application/json;charset=UTF-8'])]
    #[TestWith(['xml', 'application/xml'])]
    #[TestWith(['xml', 'application/xml, application/json'])]
    #[TestWith(['json', 'text/html, application/json;q=0.9, */*;q=0.8'])]
    #[TestWith(['xml', 'application/xml, application/json;q=0.9'])]
    #[TestWith(['json', 'application/xml;q=0.8, application/json;q=0.9'])]
    public function testBase(string $expectedBody, ?string $accept): void
    {
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/');
        if ($accept !== null) {
            $request = $request->withHeader('Accept', $accept);
        }

        $streamFactory = new StreamFactory();
        $handler = new FakeRequestHandler(new Response(body: $streamFactory->createStream('base')));
        $jsonMiddleware = new MiddlewareStub(new Response(body: $streamFactory->createStream('json')));
        $xmlMiddleware = new MiddlewareStub(new Response(body: $streamFactory->createStream('xml')));

        $middleware = new ContentNegotiatorMiddleware([
            'application/json' => $jsonMiddleware,
            'application/xml' => $xmlMiddleware,
        ]);

        $response = $middleware->process($request, $handler);

        assertSame($expectedBody, (string) $response->getBody());
    }

    public function testInvalidContentTypeThrowsException(): void
    {
        $middleware = new MiddlewareStub();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid middleware content type. A string is expected, "integer" is received.');
        new ContentNegotiatorMiddleware([
            123 => $middleware,
        ]);
    }

    public function testInvalidMiddlewareThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Invalid middleware. A "Psr\Http\Server\MiddlewareInterface" instance is expected, "string" is received.',
        );
        new ContentNegotiatorMiddleware([
            'application/json' => 'invalid',
        ]);
    }
}
