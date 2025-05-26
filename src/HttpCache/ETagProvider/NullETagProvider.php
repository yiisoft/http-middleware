<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagProvider;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETag;

/**
 * Implementation of {@see ETagProviderInterface} that returns null for all requests.
 * It can be used when ETag functionality is not required.
 */
final class NullETagProvider implements ETagProviderInterface
{
    public function get(ServerRequestInterface $request): ?ETag
    {
        return null;
    }
}
