<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETag;
use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\CallableETagGenerator;

use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\ETagGeneratorInterface;

use Yiisoft\HttpMiddleware\HttpCache\ETagHeader;

use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class ETagHeaderTest extends TestCase
{
    public function testBase(): void
    {
        $eTagHeader = new ETagHeader(
            new ETag('test'),
            new CallableETagGenerator(
                static fn(string $seed): string => 'stub-' . $seed,
            ),
        );

        assertSame('stub-test', $eTagHeader->rawValue());
        assertSame('"stub-test"', $eTagHeader->headerValue());
    }

    public function testWeak(): void
    {
        $eTagHeader = new ETagHeader(
            new ETag('test', true),
            new CallableETagGenerator(
                static fn(string $seed): string => 'stub-' . $seed,
            ),
        );

        assertSame('stub-test', $eTagHeader->rawValue());
        assertSame('W/"stub-test"', $eTagHeader->headerValue());
    }

    public function testGeneratedValueCache(): void
    {
        $generator = $this->createMock(ETagGeneratorInterface::class);
        $generator
            ->expects($this->once())
            ->method('generate')
            ->willReturn('generated-value');

        $eTagHeader = new ETagHeader(
            new ETag('test'),
            $generator,
        );

        assertSame('generated-value', $eTagHeader->rawValue());
        assertSame('"generated-value"', $eTagHeader->headerValue());
    }
}
