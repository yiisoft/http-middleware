<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\ETagValueNormalizer;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETagValueNormalizer\SuffixETagValueNormalizer;

use function PHPUnit\Framework\assertSame;

final class SuffixETagValueNormalizerTest extends TestCase
{
    #[TestWith(['tag1-gzip', 'tag1'])]
    #[TestWith(['tag1-br', 'tag1'])]
    #[TestWith(['tag1', 'tag1'])]
    #[TestWith(['gzip', 'gzip'])]
    public function testSingleSuffix(string $value, string $expected): void
    {
        $normalizer = new SuffixETagValueNormalizer(['-gzip', '-br']);

        assertSame($expected, $normalizer->normalize($value));
    }

    public function testStringSuffix(): void
    {
        $normalizer = new SuffixETagValueNormalizer('-gzip');

        assertSame('tag1', $normalizer->normalize('tag1-gzip'));
        assertSame('tag1-br', $normalizer->normalize('tag1-br'));
    }

    public function testOnlyFirstMatchingSuffixIsRemoved(): void
    {
        $normalizer = new SuffixETagValueNormalizer(['-gzip', '-br']);

        assertSame('content-gzip', $normalizer->normalize('content-gzip-br'));
    }

    public function testEmptySuffixIsIgnored(): void
    {
        $normalizer = new SuffixETagValueNormalizer('');

        assertSame('tag1', $normalizer->normalize('tag1'));
    }
}
