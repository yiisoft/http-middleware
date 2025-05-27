<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Returns `null` regardless of request.
 * It can be used when cache control functionality is not required.
 */
final class NullCacheControlProvider implements CacheControlProviderInterface
{
    public function get(ServerRequestInterface $request): ?string
    {
        return null;
    }
}
