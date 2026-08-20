<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\ETagValueNormalizer;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETagValueNormalizer\NullETagValueNormalizer;

use function PHPUnit\Framework\assertSame;

final class NullETagValueNormalizerTest extends TestCase
{
    public function testBase(): void
    {
        $normalizer = new NullETagValueNormalizer();

        assertSame('tag1', $normalizer->normalize('tag1'));
        assertSame('tag1-gzip', $normalizer->normalize('tag1-gzip'));
        assertSame('', $normalizer->normalize(''));
    }
}
