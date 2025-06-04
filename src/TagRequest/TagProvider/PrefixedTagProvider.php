<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest\TagProvider;

/**
 * A tag provider that prefixes the result of a decorated tag provider with a specified string.
 */
final class PrefixedTagProvider implements TagProviderInterface
{
    /**
     * @param string $prefix The prefix to add to the tag.
     * @param TagProviderInterface $decorated The decorated tag provider.
     */
    public function __construct(
        private readonly string $prefix,
        private readonly TagProviderInterface $decorated,
    ) {
    }

    public function get(): string
    {
        return $this->prefix . $this->decorated->get();
    }
}
