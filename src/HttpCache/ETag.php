<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache;

/**
 * Represents an ETag (Entity Tag) used for HTTP caching.
 *
 * An ETag is a unique identifier assigned to a specific version of a resource.
 * It is used by clients to determine if the resource has changed since the last request.
 */
final class ETag
{
    /**
     * @param string $seed The seed value used to generate the ETag.
     * @param bool $weak Indicates whether the ETag is weak (if the content is semantically equal, but not byte-equal).
     */
    public function __construct(
        public readonly string $seed,
        public readonly bool $weak = false,
    ) {
    }
}
