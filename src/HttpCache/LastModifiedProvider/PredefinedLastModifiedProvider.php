<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider;

use ArrayIterator;
use DateTimeImmutable;
use Iterator;
use IteratorIterator;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;

use function is_array;

/**
 * Provides dates from a predefined collection. Useful for testing purposes.
 */
final readonly class PredefinedLastModifiedProvider implements LastModifiedProviderInterface
{
    /**
     * @psalm-var Iterator<int, DateTimeImmutable>
     */
    private Iterator $iterator;

    /**
     * @param iterable $dates Predefined dates to be returned by the provider.
     *
     * @psalm-param iterable<int, DateTimeImmutable> $dates
     */
    public function __construct(iterable $dates)
    {
        $this->iterator = $this->createIterator($dates);
    }

    public function get(ServerRequestInterface $request): DateTimeImmutable
    {
        if (!$this->iterator->valid()) {
            throw new OutOfBoundsException('No more dates available.');
        }

        /** @var DateTimeImmutable $date */
        $date = $this->iterator->current();
        $this->iterator->next();

        return $date;
    }

    /**
     * @psalm-param iterable<int, DateTimeImmutable> $iterable
     * @psalm-return Iterator<int, DateTimeImmutable>
     */
    private function createIterator(iterable $iterable): Iterator
    {
        if ($iterable instanceof Iterator) {
            /** @psalm-var Iterator<int, DateTimeImmutable> */
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
