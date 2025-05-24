<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider;

use Psr\Http\Message\ServerRequestInterface;

final class ConstantCacheControlProvider implements CacheControlProviderInterface
{
    public const DEFAULT_VALUE = 'public, max-age=3600';

    public function __construct(
        private readonly string $value = self::DEFAULT_VALUE,
    ) {
    }

    public function get(ServerRequestInterface $request): ?string
    {
        return $this->value;
    }
}
