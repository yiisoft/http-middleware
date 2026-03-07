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
 *
 * @psalm-type ConditionCallable = callable(ServerRequestInterface):bool
 */
final class RedirectMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     * @psalm-var ConditionCallable
     */
    private readonly mixed $condition;

    /**
     * @param ResponseFactoryInterface $responseFactory Factory to create a response.
     * @param string $url The URL to redirect to.
     * @param int $statusCode The HTTP status code for the redirect response.
     * @param callable|null $condition Optional condition callable. If provided, the redirect is performed only when
     * the callable returns `true`. The callable receives the server request. For example:
     * ```php
     * static fn(ServerRequestInterface $request): bool => $request->getMethod() === 'POST'
     * ```
     *
     * @psalm-param ConditionCallable|null $condition
     */
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly string $url,
        private readonly int $statusCode = 301,
        ?callable $condition = null,
    ) {
        $this->condition = $condition ?? static fn(): bool => true;
    }

    /**
     * Creates a middleware with "301 Moved Permanently" status.
     *
     * @param callable|null $condition Optional condition callable.
     *
     * @psalm-param ConditionCallable|null $condition
     */
    public static function permanent(
        ResponseFactoryInterface $responseFactory,
        string $url,
        ?callable $condition = null,
    ): self {
        return new self($responseFactory, $url, 301, $condition);
    }

    /**
     * Creates a middleware with "302 Found" status.
     *
     * @param callable|null $condition Optional condition callable.
     *
     * @psalm-param ConditionCallable|null $condition
     */
    public static function found(
        ResponseFactoryInterface $responseFactory,
        string $url,
        ?callable $condition = null,
    ): self {
        return new self($responseFactory, $url, 302, $condition);
    }

    /**
     * Creates a middleware with "303 See Other" status.
     *
     * @param callable|null $condition Optional condition callable.
     *
     * @psalm-param ConditionCallable|null $condition
     */
    public static function seeOther(
        ResponseFactoryInterface $responseFactory,
        string $url,
        ?callable $condition = null,
    ): self {
        return new self($responseFactory, $url, 303, $condition);
    }

    /**
     * Creates a middleware with "307 Temporary Redirect" status.
     *
     * @param callable|null $condition Optional condition callable.
     *
     * @psalm-param ConditionCallable|null $condition
     */
    public static function temporary(
        ResponseFactoryInterface $responseFactory,
        string $url,
        ?callable $condition = null,
    ): self {
        return new self($responseFactory, $url, 307, $condition);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!($this->condition)($request)) {
            return $handler->handle($request);
        }

        return $this->responseFactory
            ->createResponse($this->statusCode)
            ->withHeader('Location', $this->url);
    }
}
