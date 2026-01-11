<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Method;

/**
 * Middleware removes the body from response for HEAD requests.
 */
final class HeadRequestMiddleware implements MiddlewareInterface
{
    /**
     * @param StreamFactoryInterface $streamFactory Factory to create a stream.
     */
    public function __construct(
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($request->getMethod() !== Method::HEAD) {
            return $response;
        }

        return $response->withBody(
            $this->streamFactory->createStream(),
        );
    }
}
