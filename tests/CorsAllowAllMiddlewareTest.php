<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests;

use HttpSoft\Message\Response;
use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\CorsAllowAllMiddleware;
use Yiisoft\HttpMiddleware\Tests\Support\FakeRequestHandler;

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
                'Allow' => ['*'],
                'Vary' => ['Origin'],
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Allow-Methods' => ['GET,OPTIONS,HEAD,POST,PUT,PATCH,DELETE'],
                'Access-Control-Allow-Headers' => ['*'],
                'Access-Control-Expose-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => ['true'],
                'Access-Control-Max-Age' => ['86400'],
            ],
            $response->getHeaders()
        );
    }
}
