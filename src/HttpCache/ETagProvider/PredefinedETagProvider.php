<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagProvider;

use ArrayIterator;
use Iterator;
use IteratorIterator;
use OutOfBoundsException;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETag;

use function is_array;

/**
 * Provides {@see ETag} from a predefined collection. Useful for testing purposes.
 */
final class PredefinedETagProvider implements ETagProviderInterface
{
    private readonly Iterator $iterator;

    /**
     * @param iterable $tags Predefined {@see ETag} to be returned by the provider.
     *
     * @psalm-param iterable<ETag> $tags
     */
    public function __construct(iterable $tags)
    {
        $this->iterator = $this->createIterator($tags);
    }

    public function get(ServerRequestInterface $request): ETag
    {
        if (!$this->iterator->valid()) {
            throw new OutOfBoundsException('No more tags available.');
        }

        $eTag = $this->iterator->current();
        $this->iterator->next();

        return $eTag;
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
