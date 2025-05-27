<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider;

use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Returns `null` for all requests.
 * It can be used when last modified functionality is not required.
 */
final class NullLastModifiedProvider implements LastModifiedProviderInterface
{
    public function get(ServerRequestInterface $request): ?DateTimeImmutable
    {
        return null;
    }
}
