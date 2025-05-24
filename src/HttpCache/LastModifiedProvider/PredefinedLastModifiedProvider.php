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
final class PredefinedLastModifiedProvider implements LastModifiedProviderInterface
{
    private readonly Iterator $iterator;

    /**
     * @param iterable $dates Predefined dates to be returned by the provider.
     *
     * @psalm-param iterable<DateTimeImmutable> $dates
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

        $date = $this->iterator->current();
        $this->iterator->next();

        return $date;
    }

    private function createIterator(iterable $iterable): Iterator
    {
        if ($iterable instanceof Iterator) {
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
