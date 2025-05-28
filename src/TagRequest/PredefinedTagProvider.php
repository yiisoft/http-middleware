<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

use ArrayIterator;
use Iterator;
use IteratorIterator;
use OutOfBoundsException;

use function is_array;

/**
 * Provides tags from a predefined collection. Useful for testing purposes.
 */
final class PredefinedTagProvider implements TagProviderInterface
{
    /**
     * @psalm-var Iterator<int, string>
     */
    private readonly Iterator $iterator;

    /**
     * @param iterable $tags Predefined tags to be returned by the provider.
     *
     * @psalm-param iterable<int, string> $tags
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

        /** @var string $tag */
        $tag = $this->iterator->current();
        $this->iterator->next();

        return $tag;
    }
    /**
     * @psalm-param iterable<int, string> $iterable
     * @psalm-return Iterator<int, string>
     */
    private function createIterator(iterable $iterable): Iterator
    {
        if ($iterable instanceof Iterator) {
            /** @psalm-var Iterator<int, string> */
            return $iterable;
        }

        if (is_array($iterable)) {
            return new ArrayIterator($iterable);
        }

        $iterator = new IteratorIterator($iterable);
        $iterator->rewind();
        return $iterator;
    }
}
