<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\ETagProvider;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETag;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\ETagSuffixRemover;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\NullETagProvider;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\PredefinedETagProvider;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

final class ETagSuffixRemoverTest extends TestCase
{
    public static function dataRemovesSuffixes(): iterable
    {
        yield ['hash', '-gzip', 'hash-gzip'];
        yield ['hash', ['-gzip'], 'hash-gzip'];
        yield ['hash', ['-gzip', '-br', '-deflate'], 'hash-br'];
        yield ['hash-gzip', ['-gzip', '-br'], 'hash-gzip-br'];
        yield ['hash-gzip', ['-deflate', '-zstd'], 'hash-gzip'];
        yield ['hash-gzip', [], 'hash-gzip'];
        yield ['hash', ['-br'], 'hash'];
    }

    #[DataProvider('dataRemovesSuffixes')]
    public function testRemovesSuffixes(
        string $expectedSeed,
        string|array $suffixes,
        string $originalSeed
    ): void {
        $remover = new ETagSuffixRemover(
            new PredefinedETagProvider([new ETag($originalSeed)]),
            $suffixes,
        );

        $eTag = $remover->get(new ServerRequest());

        assertInstanceOf(ETag::class, $eTag);
        assertSame($expectedSeed, $eTag->seed);
    }

    public function testHandlesNullETag(): void
    {
        $remover = new ETagSuffixRemover(
            new NullETagProvider(),
            '-gzip',
        );

        $result = $remover->get(new ServerRequest());

        assertNull($result);
    }

    public function testReturnsSameInstanceWhenNoModificationNeeded(): void
    {
        $eTag = new ETag('hash');
        $remover = new ETagSuffixRemover(
            new  PredefinedETagProvider([$eTag]),
            ['-gzip'],
        );

        $result = $remover->get(new ServerRequest());

        assertSame($eTag, $result);
    }
}
