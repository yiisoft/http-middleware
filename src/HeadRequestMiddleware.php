<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware removes the body from response for HEAD requests.
 */
final readonly class HeadRequestMiddleware implements MiddlewareInterface
{
    /**
     * @param StreamFactoryInterface $streamFactory Factory to create a stream.
     */
    public function __construct(
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        if ($request->getMethod() !== 'HEAD') {
            return $response;
        }

        return $response->withBody(
            $this->streamFactory->createStream(),
        );
    }
}
