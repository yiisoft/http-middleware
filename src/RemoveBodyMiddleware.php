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
 * Removes the body from the response for specific HTTP status codes, along with headers that describe
 * the now-removed body (such as `Content-Length` and `Transfer-Encoding`).
 *
 * For status codes such as `304 Not Modified`, these headers are kept by default, since per RFC 9110 / RFC 9112
 * they are still allowed to describe the representation that would have been sent in a `200 OK` response
 * to the same request, even though no body is actually sent.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc9110#section-8.6
 * @see https://datatracker.ietf.org/doc/html/rfc9112#section-6.1
 * @see https://datatracker.ietf.org/doc/html/rfc9112#section-6.3
 */
final class RemoveBodyMiddleware implements MiddlewareInterface
{
    /**
     * @param StreamFactoryInterface $streamFactory Factory to create a stream.
     * @param array $statusCodes List of HTTP status codes for which the body should be removed.
     * @param array $keepHeadersOnStatusCode List of HTTP status codes for which {@see $removedHeaders} should be
     * kept even though the body is removed.
     * @param array $removedHeaders List of headers to remove together with the body, for status codes not listed
     * in {@see $keepHeadersOnStatusCode}.
     *
     * @psalm-param list<int> $statusCodes
     * @psalm-param list<int> $keepHeadersOnStatusCode
     * @psalm-param list<non-empty-string> $removedHeaders
     */
    public function __construct(
        private readonly StreamFactoryInterface $streamFactory,
        private readonly array $statusCodes = [
            100, // Continue
            101, // Switching Protocols
            102, // Processing
            103, // Early Hints
            204, // No Content
            205, // Reset Content
            304, // Not Modified
        ],
        private readonly array $keepHeadersOnStatusCode = [
            304, // Not Modified
        ],
        private readonly array $removedHeaders = [
            'Content-Length',
            'Transfer-Encoding',
        ],
    ) {}

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
        $response = $response->withBody(
            $this->streamFactory->createStream(),
        );

        if (in_array($response->getStatusCode(), $this->keepHeadersOnStatusCode, true)) {
            return $response;
        }

        foreach ($this->removedHeaders as $header) {
            $response = $response->withoutHeader($header);
        }

        return $response;
    }
}
