<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface for cache control header value providers.
 */
interface CacheControlProviderInterface
{
    /**
     * Returns a cache control header value for the given server request.
     *
     * @param ServerRequestInterface $request The server request for which to generate the cache control value.
     * @return string|null The cache control header value or null if no cache control is applicable.
     */
    public function get(ServerRequestInterface $request): ?string;
}
