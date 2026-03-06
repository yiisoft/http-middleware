<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware that responds with a redirect to the specified URL.
 */
final class RedirectMiddleware implements MiddlewareInterface
{
    /**
     * @param ResponseFactoryInterface $responseFactory Factory to create a response.
     * @param string $url The URL to redirect to.
     * @param int $statusCode The HTTP status code for the redirect response.
     */
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly string $url,
        private readonly int $statusCode = 301,
    ) {}

    /**
     * Creates a middleware with "301 Moved Permanently" status.
     */
    public static function permanent(ResponseFactoryInterface $responseFactory, string $url): self
    {
        return new self($responseFactory, $url, 301);
    }

    /**
     * Creates a middleware with "302 Found" status.
     */
    public static function found(ResponseFactoryInterface $responseFactory, string $url): self
    {
        return new self($responseFactory, $url, 302);
    }

    /**
     * Creates a middleware with "303 See Other" status.
     */
    public static function seeOther(ResponseFactoryInterface $responseFactory, string $url): self
    {
        return new self($responseFactory, $url, 303);
    }

    /**
     * Creates a middleware with "307 Temporary Redirect" status.
     */
    public static function temporary(ResponseFactoryInterface $responseFactory, string $url): self
    {
        return new self($responseFactory, $url, 307);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse($this->statusCode)
            ->withHeader('Location', $this->url);
    }
}
