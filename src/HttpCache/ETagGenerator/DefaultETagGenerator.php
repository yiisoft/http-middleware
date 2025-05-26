<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagGenerator;

/**
 * Generates a string ETag value using a PHP native function {@see base64_encode()} and {@see sha1()}.
 */
final class DefaultETagGenerator implements ETagGeneratorInterface
{
    public function generate(string $seed): string
    {
        return rtrim(base64_encode(sha1($seed, true)), '=');
    }
}
