<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagValueNormalizer;

/**
 * Normalizes raw ETag values obtained from the `If-None-Match` request header before comparing them with the
 * application generated ETag value.
 *
 * Normalization is needed when an intermediary (for example, a web server compression module such as Apache
 * `mod_deflate` or `mod_brotli`) modifies the ETag header value by appending a suffix (`-gzip`, `-br`, etc.).
 */
interface ETagValueNormalizerInterface
{
    /**
     * Returns the normalized ETag value.
     *
     * @param string $value The raw ETag value (without quotes and `W/` prefix) to normalize.
     * @return string The normalized ETag value.
     */
    public function normalize(string $value): string;
}
