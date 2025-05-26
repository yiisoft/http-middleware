<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Implementation of {@see CacheControlProviderInterface} that returns a predefined cache control header value for all
 * requests.
 */
final class ConstantCacheControlProvider implements CacheControlProviderInterface
{
    public const DEFAULT_VALUE = 'public, max-age=3600';

    /**
     * @param string $value The cache control header value to return.
     */
    public function __construct(
        private readonly string $value = self::DEFAULT_VALUE,
    ) {
    }

    public function get(ServerRequestInterface $request): ?string
    {
        return $this->value;
    }
}
