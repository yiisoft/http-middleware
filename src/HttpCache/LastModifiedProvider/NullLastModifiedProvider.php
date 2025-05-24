<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider;

use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;

final class NullLastModifiedProvider implements LastModifiedProviderInterface
{
    public function get(ServerRequestInterface $request): ?DateTimeImmutable
    {
        return null;
    }
}
