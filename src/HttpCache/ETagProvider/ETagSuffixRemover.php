<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagProvider;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETag;

use function strlen;

/**
 * A decorator for {@see ETagProviderInterface} that removes suffixes from ETag seeds.
 *
 * This decorator wraps another ETag provider and modifies the ETag seed by removing
 * the first matching suffix from the provided list. This is useful for normalizing
 * ETag values by stripping compression algorithms suffixes.
 *
 * Note: only the first matching suffix is removed. E.g., of provider returns ETag with seed 'content-br-gzip',
 * the remover with suffixes `['-br', '-gzip']` will return ETag with seed 'content-br'.
 *
 * Example usage:
 * ```php
 * $provider = new SomeETagProvider();
 *
 * // Single suffix as string
 * $remover = new ETagSuffixRemover($provider, '-gzip');
 *
 * // Multiple suffixes as array
 * $remover = new ETagSuffixRemover($provider, ['-gzip', '-br']);
 * ```
 */
final class ETagSuffixRemover implements ETagProviderInterface
{
    /**
     * @var string[]
     */
    private readonly array $suffixes;

    /**
     * @param ETagProviderInterface $provider The wrapped ETag provider.
     * @param string|string[] $suffix A suffix or array of suffixes to remove from ETag seeds.
     */
    public function __construct(
        private readonly ETagProviderInterface $provider,
        string|array $suffix = [],
    ) {
        $this->suffixes = (array) $suffix;
    }

    public function get(ServerRequestInterface $request): ?ETag
    {
        $eTag = $this->provider->get($request);

        if ($eTag === null) {
            return null;
        }

        foreach ($this->suffixes as $suffix) {
            if (str_ends_with($eTag->seed, $suffix)) {
                return $eTag->withSeed(
                    substr($eTag->seed, 0, -strlen($suffix))
                );
            }
        }

        return $eTag;
    }
}
