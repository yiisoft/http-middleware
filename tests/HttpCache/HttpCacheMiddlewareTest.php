<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache;

use DateTimeImmutable;
use HttpSoft\Message\Response;
use HttpSoft\Message\ResponseFactory;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\ConstantCacheControlProvider;
use Yiisoft\HttpMiddleware\HttpCache\ETag;
use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\CallableETagGenerator;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\NullETagProvider;
use Yiisoft\HttpMiddleware\HttpCache\HttpCacheMiddleware;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\PredefinedETagProvider;
use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\NullLastModifiedProvider;
use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\PredefinedLastModifiedProvider;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertSame;

final class HttpCacheMiddlewareTest extends TestCase
{
    #[TestWith(['POST'])]
    #[TestWith(['PUT'])]
    #[TestWith(['DELETE'])]
    #[TestWith(['PATCH'])]
    #[TestWith(['OPTIONS'])]
    public function testNotSupportedMethods(string $method): void
    {
        $request = new ServerRequest(method: $method);
        $sourceResponse = new Response();
        $requestHandler = new FakeRequestHandler($sourceResponse);
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            cacheControlProvider: new ConstantCacheControlProvider(),
        );

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame($sourceResponse, $response);
    }

    #[TestWith(['GET'])]
    #[TestWith(['HEAD'])]
    public function testDefault(string $method): void
    {
        $request = new ServerRequest(method: $method);
        $sourceResponse = new Response();
        $requestHandler = new FakeRequestHandler($sourceResponse);
        $middleware = new HttpCacheMiddleware(new ResponseFactory());

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame($sourceResponse, $response);
    }

    public function testWithETagProvider(): void
    {
        $request = new ServerRequest();
        $requestHandler = new FakeRequestHandler(new Response());
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            eTagProvider: new PredefinedETagProvider([
                new ETag('tag1'),
            ])
        );

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());

        /**
         * By default, {see DefaultETagGenerator} is used to generate ETag header value.
         */
        assertSame(['ETag' => ['"72izm+g7ExSlKtEdn20dTJGWcjk"']], $response->getHeaders());
    }

    public function testWithCacheControlProvider(): void
    {
        $request = new ServerRequest();
        $requestHandler = new FakeRequestHandler(new Response());
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            cacheControlProvider: new ConstantCacheControlProvider('public, max-age=7200'),
        );

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame(['Cache-Control' => ['public, max-age=7200']], $response->getHeaders());
    }

    public function testWithLastModifiedProvider(): void
    {
        $request = new ServerRequest();
        $requestHandler = new FakeRequestHandler(new Response());
        $date = new DateTimeImmutable('2023-10-01 12:00:15 UTC');
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            lastModifiedProvider: new PredefinedLastModifiedProvider([$date]),
        );

        $response = $middleware->process($request, $requestHandler);

        assertSame($request, $requestHandler->getLastRequest());
        assertSame(
            [
                'Last-Modified' => ['Sun, 01 Oct 2023 12:00:15 GMT']
            ],
            $response->getHeaders(),
        );
    }

    public function testIfNoneMatchNotEquals(): void
    {
        $request = new ServerRequest(
            headers: [
                'If-None-Match' => ['"tag1"'],
            ]
        );
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            eTagProvider: new PredefinedETagProvider([new ETag('test')]),
            eTagGenerator: new CallableETagGenerator(
                static fn(string $seed) => $seed . '-value',
            )
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(200, $response->getStatusCode());
        assertSame(
            [
                'ETag' => ['"test-value"'],
            ],
            $response->getHeaders(),
        );
    }

    #[TestWith([['"tag1"']])]
    #[TestWith([['W/"tag1"']])]
    #[TestWith([['"tag0"', '"tag1"']])]
    #[TestWith([['"tag0"', 'W/"tag1"']])]
    public function testIfNoneMatchEquals(array $ifNoneMatchValues): void
    {
        $request = new ServerRequest(
            headers: [
                'If-None-Match' => $ifNoneMatchValues,
            ]
        );
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            eTagProvider: new PredefinedETagProvider([new ETag('tag1')]),
            eTagGenerator: new CallableETagGenerator(
                static fn(string $seed) => $seed,
            )
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(304, $response->getStatusCode());
        assertSame(
            [
                'ETag' => ['"tag1"'],
            ],
            $response->getHeaders(),
        );
    }

    public function testIfNoneMatchWithoutEtag(): void
    {
        $request = new ServerRequest(
            headers: [
                'If-None-Match' => ['"tag1"'],
            ]
        );
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            eTagProvider: new NullETagProvider(),
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(200, $response->getStatusCode());
        assertSame([], $response->getHeaders());
    }

    public function testEmptyIfNoneMatch(): void
    {
        $request = new ServerRequest(
            headers: [
                'If-None-Match' => [''],
            ]
        );
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            eTagProvider: new PredefinedETagProvider([new ETag('test')]),
            eTagGenerator: new CallableETagGenerator(
                static fn(string $seed) => $seed . '-value',
            )
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(200, $response->getStatusCode());
        assertSame(
            [
                'ETag' => ['"test-value"'],
            ],
            $response->getHeaders(),
        );
    }

    public function testIfModifiedSinceTrue(): void
    {
        $request = new ServerRequest(
            headers: [
                'If-Modified-Since' => 'Sat, 25 May 2024 10:00:00 GMT',
            ]
        );
        $date = new DateTimeImmutable('Sun, 26 May 2024 12:30:00 GMT');
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            lastModifiedProvider: new PredefinedLastModifiedProvider([$date]),
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(200, $response->getStatusCode());
        assertSame(
            [
                'Last-Modified' => ['Sun, 26 May 2024 12:30:00 GMT'],
            ],
            $response->getHeaders(),
        );
    }

    #[TestWith(['Sat, 25 May 2024 10:00:00 GMT'])]
    #[TestWith(['Fri, 24 May 2024 12:30:00 GMT'])]
    public function testIfModifiedSinceFalse(string $ifModifiedSinceValue): void
    {
        $request = new ServerRequest(
            headers: [
                'If-Modified-Since' => $ifModifiedSinceValue,
            ]
        );
        $date = new DateTimeImmutable('Fri, 24 May 2024 12:30:00 GMT');
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            lastModifiedProvider: new PredefinedLastModifiedProvider([$date]),
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(304, $response->getStatusCode());
        assertSame(
            [
                'Last-Modified' => ['Fri, 24 May 2024 12:30:00 GMT'],
            ],
            $response->getHeaders(),
        );
    }

    public function testIfModifiedSinceWithoutLastModified(): void
    {
        $request = new ServerRequest(
            headers: [
                'If-Modified-Since' => 'Sat, 25 May 2024 10:00:00 GMT',
            ]
        );
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            lastModifiedProvider: new NullLastModifiedProvider(),
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(200, $response->getStatusCode());
        assertSame([], $response->getHeaders());
    }

    public function testIfModifiedSinceWithInvalidDate(): void
    {
        $request = new ServerRequest(
            headers: [
                'If-Modified-Since' => 'XXX',
            ]
        );
        $date = new DateTimeImmutable('Fri, 24 May 2024 12:30:00 GMT');
        $middleware = new HttpCacheMiddleware(
            new ResponseFactory(),
            lastModifiedProvider: new PredefinedLastModifiedProvider([$date]),
        );

        $response = $middleware->process($request, new FakeRequestHandler());

        assertSame(200, $response->getStatusCode());
        assertSame(
            [
                'Last-Modified' => ['Fri, 24 May 2024 12:30:00 GMT'],
            ],
            $response->getHeaders(),
        );
    }
}
