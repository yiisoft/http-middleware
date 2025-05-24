<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider;

use Psr\Http\Message\ServerRequestInterface;

final class NullCacheControlProvider implements CacheControlProviderInterface
{
    public function get(ServerRequestInterface $request): ?string
    {
        return null;
    }
}
