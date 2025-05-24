<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\ETagProvider;

use ArrayIterator;
use ArrayObject;
use HttpSoft\Message\ServerRequest;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETag;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\PredefinedETagProvider;

use function PHPUnit\Framework\assertSame;

final class PredefinedETagProviderTest extends TestCase
{
    public function testArray(): void
    {
        $request = new ServerRequest();

        $eTag1 = new ETag('tag1');
        $eTag2 = new ETag('tag2');
        $provider = new PredefinedETagProvider([$eTag1, $eTag2]);

        assertSame($eTag1, $provider->get($request));
        assertSame($eTag2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testGenerator(): void
    {
        $request = new ServerRequest();

        $eTag1 = new ETag('tag1');
        $eTag2 = new ETag('tag2');
        $generator = static function () use ($eTag1, $eTag2) {
            yield $eTag1;
            yield $eTag2;
        };

        $provider = new PredefinedETagProvider($generator());

        assertSame($eTag1, $provider->get($request));
        assertSame($eTag2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testStartedIterator(): void
    {
        $request = new ServerRequest();

        $eTag1 = new ETag('tag1');
        $eTag2 = new ETag('tag2');
        $iterator = new ArrayIterator([$eTag1, $eTag2]);
        $iterator->next();

        $provider = new PredefinedETagProvider($iterator);

        assertSame($eTag2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testArrayObject(): void
    {
        $request = new ServerRequest();

        $eTag1 = new ETag('tag1');
        $eTag2 = new ETag('tag2');
        $provider = new PredefinedETagProvider(
            new ArrayObject([$eTag1, $eTag2]),
        );

        assertSame($eTag1, $provider->get($request));
        assertSame($eTag2, $provider->get($request));

        $this->expectException(OutOfBoundsException::class);
        $provider->get($request);
    }

    public function testOutOfBoundsException(): void
    {
        $request = new ServerRequest();
        $provider = new PredefinedETagProvider([]);

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('No more tags available.');
        $provider->get($request);
    }
}
