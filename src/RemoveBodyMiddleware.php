<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

/**
 * Removes the body from the response for specific HTTP status codes.
 */
final class RemoveBodyMiddleware implements MiddlewareInterface
{
    /**
     * @param StreamFactoryInterface $streamFactory Factory to create a stream.
     * @param array $statusCodes List of HTTP status codes for which the body should be removed.
     *
     * @psalm-param list<int> $statusCodes
     */
    public function __construct(
        private readonly StreamFactoryInterface $streamFactory,
        private readonly array $statusCodes = [
            100, // Continue
            101, // Switching Protocols
            102, // Processing
            204, // No Content
            205, // Reset Content
            304, // Not Modified
        ],
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return $this->shouldRemoveBody($response)
            ? $this->removeBody($response)
            : $response;
    }

    private function shouldRemoveBody(ResponseInterface $response): bool
    {
        return in_array($response->getStatusCode(), $this->statusCodes, true);
    }

    private function removeBody(ResponseInterface $response): ResponseInterface
    {
        return $response->withBody(
            $this->streamFactory->createStream(),
        );
    }
}
