<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Yiisoft\Http\HeaderValueHelper;

use function gettype;
use function is_string;
use function sprintf;
use function str_contains;

/**
 * Content negotiation by delegating request handling to specific middlewares based on the `Accept` header.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Content_negotiation
 */
final class ContentNegotiatorMiddleware implements MiddlewareInterface
{
    /**
     * @psalm-var array<string, MiddlewareInterface>
     */
    private array $middlewares;

    /**
     * @param array $middlewares The array key is the content type, and the value is an instance of
     * {@see MiddlewareInterface}.
     *
     * @psalm-param array<string, MiddlewareInterface> $middlewares
     */
    public function __construct(array $middlewares)
    {
        $this->checkMiddlewares($middlewares);
        $this->middlewares = $middlewares;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $accepted = HeaderValueHelper::getSortedAcceptTypes(
            $request->getHeader('Accept'),
        );

        foreach ($accepted as $accept) {
            foreach ($this->middlewares as $contentType => $middleware) {
                if (str_contains($accept, $contentType)) {
                    return $middleware->process($request, $handler);
                }
            }
        }

        return $handler->handle($request);
    }

    /**
     * Checks the content middlewares.
     *
     * @param array $middlewares The content middlewares to check.
     */
    private function checkMiddlewares(array $middlewares): void
    {
        foreach ($middlewares as $contentType => $middleware) {
            if (!is_string($contentType)) {
                throw new RuntimeException(
                    sprintf(
                        'Invalid middleware content type. A string is expected, "%s" is received.',
                        gettype($contentType),
                    ),
                );
            }

            if (!($middleware instanceof MiddlewareInterface)) {
                throw new RuntimeException(
                    sprintf(
                        'Invalid middleware. A "%s" instance is expected, "%s" is received.',
                        MiddlewareInterface::class,
                        get_debug_type($middleware),
                    ),
                );
            }
        }
    }
}
