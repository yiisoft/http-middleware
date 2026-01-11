<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Header;
use Yiisoft\Http\Method;

/**
 * Adds Cross-Origin Resource Sharing (CORS) headers allowing everything to the response.
 *
 * Security notice.
 * This middleware should not be used in production as-is unless you're absolutely certain it's safe
 * for your context. Allowing all origins and credentials without restriction poses a serious security risk.
 *
 * @see https://developer.mozilla.org/docs/Web/HTTP/Guides/CORS
 */
final class CorsAllowAllMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $response
            ->withHeader(Header::ALLOW, '*')
            ->withHeader(Header::VARY, 'Origin')
            ->withHeader(Header::ACCESS_CONTROL_ALLOW_ORIGIN, '*')
            ->withHeader(Header::ACCESS_CONTROL_ALLOW_METHODS, implode(',', Method::ALL))
            ->withHeader(Header::ACCESS_CONTROL_ALLOW_HEADERS, '*')
            ->withHeader(Header::ACCESS_CONTROL_EXPOSE_HEADERS, '*')
            ->withHeader(Header::ACCESS_CONTROL_ALLOW_CREDENTIALS, 'true')
            ->withHeader(Header::ACCESS_CONTROL_MAX_AGE, '86400');
    }
}
