<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\TagRequest;

use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\TagRequest\PredefinedTagProvider;
use Yiisoft\HttpMiddleware\TagRequest\TagRequestMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

use function PHPUnit\Framework\assertIsString;
use function PHPUnit\Framework\assertMatchesRegularExpression;
use function PHPUnit\Framework\assertSame;

final class TagRequestMiddlewareTest extends TestCase
{
    public function testBase(): void
    {
        $expectedResponse = new Response();
        $requestHandler = new FakeRequestHandler($expectedResponse);
        $middleware = new TagRequestMiddleware();

        $response = $middleware->process(new ServerRequest(), $requestHandler);

        assertSame($expectedResponse, $response);

        $tag = $requestHandler->getLastRequest()->getAttribute('requestTag');
        assertIsString($tag);
        assertMatchesRegularExpression('/^[0-9a-f]+\.[0-9]+$/', $tag);
    }

    public function testCustomAttributeName(): void
    {
        $requestHandler = new FakeRequestHandler();
        $middleware = new TagRequestMiddleware('customTag');

        $middleware->process(new ServerRequest(), $requestHandler);

        $tag = $requestHandler->getLastRequest()->getAttribute('customTag');
        assertIsString($tag);
    }

    public function testCustomTagProvider(): void
    {
        $requestHandler = new FakeRequestHandler();
        $middleware = new TagRequestMiddleware(
            tagProvider: new PredefinedTagProvider(['tag1']),
        );

        $middleware->process(new ServerRequest(), $requestHandler);

        $tag = $requestHandler->getLastRequest()->getAttribute('requestTag');
        assertSame('tag1', $tag);
    }
}
