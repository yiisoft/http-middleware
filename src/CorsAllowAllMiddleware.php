<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function count;
use function in_array;

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
    /**
     * @param ResponseFactoryInterface|null $responseFactory Factory used to short-circuit preflight requests.
     */
    public function __construct(
        private readonly ?ResponseFactoryInterface $responseFactory = null,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origins = $request->getHeader('Origin');
        $origin = count($origins) === 1 ? $origins[0] : '';
        $isPreflight = $request->getMethod() === 'OPTIONS'
            && $request->getHeaderLine('Access-Control-Request-Method') !== '';

        $response = $isPreflight && $this->responseFactory !== null
            ? $this->responseFactory->createResponse(204)
            : $handler->handle($request);

        $exposedHeaders = [];
        /** @var array<string, string[]> $headers */
        $headers = $response->getHeaders();
        foreach ($headers as $name => $_) {
            if (strtolower($name) !== 'set-cookie') {
                $exposedHeaders[] = $name;
            }
        }

        $vary = array_map(trim(...), explode(',', strtolower($response->getHeaderLine('Vary'))));
        if (!in_array('origin', $vary, true)) {
            $response = $response->withAddedHeader('Vary', 'Origin');
        }

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin === '' ? '*' : $origin)
            ->withHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,HEAD,POST,PUT,PATCH,DELETE')
            ->withHeader('Access-Control-Max-Age', '86400');

        if ($origin === '') {
            return $response
                ->withHeader('Access-Control-Allow-Headers', '*')
                ->withHeader('Access-Control-Expose-Headers', '*');
        }

        $requestedHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
        if ($requestedHeaders !== '') {
            $response = $response->withHeader('Access-Control-Allow-Headers', $requestedHeaders);
        }
        if ($exposedHeaders !== []) {
            $response = $response->withHeader('Access-Control-Expose-Headers', implode(',', $exposedHeaders));
        }

        return $response->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
