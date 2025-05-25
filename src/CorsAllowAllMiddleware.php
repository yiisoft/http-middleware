<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Adds Cross-Origin Resource Sharing (CORS) headers allowing everything to the response.
 *
 * @see https://developer.mozilla.org/docs/Web/HTTP/Guides/CORS
 */
final class CorsAllowAllMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $response
            ->withHeader('Allow', '*')
            ->withHeader('Vary', 'Origin')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,HEAD,POST,PUT,PATCH,DELETE')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Expose-Headers', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
