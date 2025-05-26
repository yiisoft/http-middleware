<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

/**
 * Provides a tag for a request.
 */
interface TagProviderInterface
{
    /**
     * Returns a tag for a request.
     *
     * @return string The tag.
     */
    public function get(): string;
}
