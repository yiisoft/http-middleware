<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

use ArrayIterator;
use Iterator;
use IteratorIterator;
use OutOfBoundsException;

use function is_array;

final class PredefinedTagProvider implements TagProviderInterface
{
    private Iterator $iterator;

    /**
     * @psalm-param iterable<string> $tags
     */
    public function __construct(iterable $tags)
    {
        $this->iterator = $this->createIterator($tags);
    }

    public function get(): string
    {
        if (!$this->iterator->valid()) {
            throw new OutOfBoundsException('No more tags available.');
        }

        $tag = $this->iterator->current();
        $this->iterator->next();

        return $tag;
    }

    private function createIterator(iterable $iterable): Iterator
    {
        return match (true) {
            $iterable instanceof Iterator => $iterable,
            is_array($iterable) => new ArrayIterator($iterable),
            default => new IteratorIterator($iterable),
        };
    }
}
