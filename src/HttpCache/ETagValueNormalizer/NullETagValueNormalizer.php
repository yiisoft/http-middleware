<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagValueNormalizer;

/**
 * Returns ETag values unmodified. It can be used when ETag normalization is not required.
 */
final class NullETagValueNormalizer implements ETagValueNormalizerInterface
{
    public function normalize(string $value): string
    {
        return $value;
    }
}
