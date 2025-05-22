<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

final class PrefixedTagProvider implements TagProviderInterface
{
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
