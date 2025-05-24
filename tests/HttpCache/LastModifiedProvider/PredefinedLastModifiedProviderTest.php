<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\LastModifiedProvider;

use ArrayIterator;
use ArrayObject;
use DateTimeImmutable;
use HttpSoft\Message\ServerRequest;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\PredefinedLastModifiedProvider;

use function PHPUnit\Framework\assertSame;

final class PredefinedLastModifiedProviderTest extends TestCase
{
    public function testArray(): void
    {
        $request = new ServerRequest();

        $date1 = new DateTimeImmutable();
        $date2 = new DateTimeImmutable();
        $provider = new PredefinedLastModifiedProvider([$date1, $date2]);

        assertSame($date1, $provider->get($request));
        assertSame($date2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testGenerator(): void
    {
        $request = new ServerRequest();

        $date1 = new DateTimeImmutable();
        $date2 = new DateTimeImmutable();
        $generator = static function () use ($date1, $date2) {
            yield $date1;
            yield $date2;
        };

        $provider = new PredefinedLastModifiedProvider($generator());

        assertSame($date1, $provider->get($request));
        assertSame($date2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testStartedIterator(): void
    {
        $request = new ServerRequest();

        $date1 = new DateTimeImmutable();
        $date2 = new DateTimeImmutable();
        $iterator = new ArrayIterator([$date1, $date2]);
        $iterator->next();

        $provider = new PredefinedLastModifiedProvider($iterator);

        assertSame($date2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testArrayObject(): void
    {
        $request = new ServerRequest();

        $date1 = new DateTimeImmutable();
        $date2 = new DateTimeImmutable();
        $provider = new PredefinedLastModifiedProvider(
            new ArrayObject([$date1, $date2]),
        );

        assertSame($date1, $provider->get($request));
        assertSame($date2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testOutOfBoundsException(): void
    {
        $request = new ServerRequest();
        $provider = new PredefinedLastModifiedProvider([]);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('No more dates available.');
        $provider->get($request);
    }
}
