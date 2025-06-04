<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\ForceSecureConnection;

/**
 * Redirection HTTP to HTTPS options.
 */
final class RedirectOptions
{
    /**
     * @param bool $enabled Whether to enable redirection.
     * @param int|null $port The redirection port.
     */
    public function __construct(
        public readonly bool $enabled = true,
        public readonly ?int $port = null,
    ) {
    }
}
