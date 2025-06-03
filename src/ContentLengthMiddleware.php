<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

/**
 * Configurable middleware that adds or removes the `Content-Length` header from the response.
 */
final readonly class ContentLengthMiddleware implements MiddlewareInterface
{
    /**
     * @param bool $removeOnTransferEncoding Whether to remove the `Content-Length` header if `Transfer-Encoding` header
     * is present.
     * @param bool $add Whether to add the `Content-Length` header if not present.
     * @param array $doNotAddOnStatusCode List of HTTP status codes where `Content-Length` header should not be added.
     *
     * @psalm-param list<int> $doNotAddOnStatusCode
     */
    public function __construct(
        private bool $removeOnTransferEncoding = true,
        private bool $add = true,
        private array $doNotAddOnStatusCode = [
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

        if ($this->shouldRemoveContentLength($response)) {
            return $response->withoutHeader('Content-Length');
        }

        if ($this->shouldSkipContentLength($response)) {
            return $response;
        }

        return $this->addContentLength($response);
    }

    private function shouldRemoveContentLength(ResponseInterface $response): bool
    {
        return $this->removeOnTransferEncoding && $response->hasHeader('Transfer-Encoding');
    }

    private function shouldSkipContentLength(ResponseInterface $response): bool
    {
        return !$this->add
            || $response->hasHeader('Content-Length')
            || in_array($response->getStatusCode(), $this->doNotAddOnStatusCode, true);
    }

    private function addContentLength(ResponseInterface $response): ResponseInterface
    {
        $body = $response->getBody();
        if (!$body->isReadable()) {
            return $response;
        }

        $contentLength = $body->getSize();
        if ($contentLength === null || $contentLength === 0) {
            return $response;
        }

        return $response->withHeader('Content-Length', (string) $contentLength);
    }
}
