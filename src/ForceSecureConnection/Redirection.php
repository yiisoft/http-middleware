<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\ForceSecureConnection;

/**
 * Redirection from HTTP to HTTPS parameters.
 */
final class Redirection
{
    /**
     * @param int $statusCode The response status code of redirection.
     * @param int|null $port The redirection port.
     */
    public function __construct(
        public readonly int $statusCode = 301, // 301 Moved Permanently
        public readonly ?int $port = null,
    ) {
    }
}
