<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagValueNormalizer;

use function str_ends_with;
use function strlen;
use function substr;

/**
 * Removes a suffix from ETag values.
 *
 * This is useful for normalizing ETag values modified by web server compression modules. For example, Apache
 * `mod_deflate` and `mod_brotli` append `-gzip` and `-br` suffixes to the ETag header value
 * (see {@link https://httpd.apache.org/docs/2.4/mod/mod_deflate.html#deflatealteretag}).
 *
 * Only the first matching suffix is removed. For example, for value `content-gzip-br` and suffixes
 * `['-gzip', '-br']`, the result is `content-gzip`.
 */
final class SuffixETagValueNormalizer implements ETagValueNormalizerInterface
{
    /**
     * @var string[] The suffixes to remove.
     */
    private readonly array $suffixes;

    /**
     * @param string|string[] $suffix A single suffix or a list of suffixes to remove from ETag values.
     *
     * @psalm-param string|list<string> $suffix
     */
    public function __construct(string|array $suffix)
    {
        $this->suffixes = (array) $suffix;
    }

    public function normalize(string $value): string
    {
        foreach ($this->suffixes as $suffix) {
            if ($suffix !== '' && str_ends_with($value, $suffix)) {
                return substr($value, 0, -strlen($suffix));
            }
        }
        return $value;
    }
}
