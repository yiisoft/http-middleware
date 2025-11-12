<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETag;

use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class ETagTest extends TestCase
{
    public function testWithSeed(): void
    {
        $eTag = new ETag('foo', true);

        $newETag = $eTag->withSeed('boo');

        assertNotSame($eTag, $newETag);
        assertSame('foo', $eTag->seed);
        assertTrue($eTag->weak);
        assertSame('boo', $newETag->seed);
        assertTrue($newETag->weak);
    }
}
