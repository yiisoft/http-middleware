<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

final class ContentLengthMiddleware implements MiddlewareInterface
{
    public function __construct(
        public bool $removeOnTransferEncoding = true,
        public bool $add = true,
        public array $doNotAddOnStatusCode = [
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

        if ($this->removeOnTransferEncoding && $response->hasHeader('Transfer-Encoding')) {
            return $response->withoutHeader('Content-Length');
        }

        if (!$this->add || $response->hasHeader('Content-Length')) {
            return $response;
        }

        if (in_array($response->getStatusCode(), $this->doNotAddOnStatusCode, true)) {
            return $response;
        }

        $contentLength = $this->getBodySize($response->getBody());
        if ($contentLength === null) {
            return $response;
        }

        return $response->withHeader('Content-Length', (string) $contentLength);
    }

    private function getBodySize(StreamInterface $body): ?int
    {
        if (!$body->isReadable()) {
            return null;
        }

        $size = $body->getSize();

        return $size === 0 ? null : $size;
    }
}
