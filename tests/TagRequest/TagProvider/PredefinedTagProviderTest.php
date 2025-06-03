<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\TagRequest\TagProvider;

use ArrayIterator;
use ArrayObject;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\TagRequest\TagProvider\PredefinedTagProvider;

use function PHPUnit\Framework\assertSame;

final class PredefinedTagProviderTest extends TestCase
{
    public function testArray(): void
    {
        $provider = new PredefinedTagProvider(['tag1', 'tag2', 'tag3']);

        assertSame('tag1', $provider->get());
        assertSame('tag2', $provider->get());
        assertSame('tag3', $provider->get());

        $this->expectException(OutOfBoundsException::class);
        $provider->get();
    }

    public function testGenerator(): void
    {
        $generator = static function () {
            yield 'tag1';
            yield 'tag2';
        };

        $provider = new PredefinedTagProvider($generator());

        assertSame('tag1', $provider->get());
        assertSame('tag2', $provider->get());

        $this->expectException(OutOfBoundsException::class);
        $provider->get();
    }

    public function testStartedIterator(): void
    {
        $iterator = new ArrayIterator(['tag1', 'tag2']);
        $iterator->next();

        $provider = new PredefinedTagProvider($iterator);

        assertSame('tag2', $provider->get());

        $this->expectException(OutOfBoundsException::class);
        $provider->get();
    }

    public function testArrayObject(): void
    {
        $provider = new PredefinedTagProvider(
            new ArrayObject(['tag1', 'tag2']),
        );

        assertSame('tag1', $provider->get());
        assertSame('tag2', $provider->get());

        $this->expectException(OutOfBoundsException::class);
        $provider->get();
    }

    public function testOutOfBoundsException(): void
    {
        $provider = new PredefinedTagProvider([]);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('No more tags available.');
        $provider->get();
    }
}
