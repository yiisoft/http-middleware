<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagProvider;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETag;

final class NullETagProvider implements ETagProviderInterface
{
    public function get(ServerRequestInterface $request): ?ETag
    {
        return null;
    }
}
