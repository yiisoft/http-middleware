<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface for Cache-Control header value providers. Given a request it generates a header value.
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control
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
