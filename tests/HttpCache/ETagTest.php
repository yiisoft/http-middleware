<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETag;
use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\CallableETagGenerator;

use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\ETagGeneratorInterface;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class ETagTest extends TestCase
{
    public function testBase(): void
    {
        $generator = new CallableETagGenerator(
            static fn(string $seed): string => 'stub-' . $seed,
        );
        $eTag = new ETag('test');

        assertSame('test', $eTag->seed);
        assertFalse($eTag->weak);
        assertSame('stub-test', $eTag->rawValue($generator));
        assertSame('"stub-test"', $eTag->headerValue($generator));
    }

    public function testWeak(): void
    {
        $generator = new CallableETagGenerator(
            static fn(string $seed): string => 'stub-' . $seed,
        );
        $eTag = new ETag('test', true);

        assertSame('test', $eTag->seed);
        assertTrue($eTag->weak);
        assertSame('stub-test', $eTag->rawValue($generator));
        assertSame('W/"stub-test"', $eTag->headerValue($generator));
    }

    public function testGeneratedValueCache(): void
    {
        $generator = $this->createMock(ETagGeneratorInterface::class);
        $generator
            ->expects($this->once())
            ->method('generate')
            ->willReturn('generated-value');

        $eTag = new ETag('test');

        assertSame('generated-value', $eTag->rawValue($generator));
        assertSame('"generated-value"', $eTag->headerValue($generator));
    }
}
