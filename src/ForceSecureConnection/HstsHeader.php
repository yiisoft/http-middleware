<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\ForceSecureConnection;

/**
 * The `Strict-Transport-Security` header to be added to the response.
 */
final class HstsHeader
{
    public const DEFAULT_MAX_AGE = 31_536_000; // 12 months

    /**
     * @param int $maxAge The max age.
     * @param bool $subdomains Whether to add the `includeSubDomains` option to the header value.
     */
    public function __construct(
        public readonly int $maxAge = self::DEFAULT_MAX_AGE,
        public readonly bool $subdomains = false,
    ) {
    }

    public function getValue(): string
    {
        $value = 'max-age=' . $this->maxAge;
        if ($this->subdomains) {
            $value .= '; includeSubDomains';
        }
        return $value;
    }
}
