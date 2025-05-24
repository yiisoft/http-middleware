<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagGenerator;

final class DefaultETagGenerator implements ETagGeneratorInterface
{
    public function generate(string $seed): string
    {
        return rtrim(base64_encode(sha1($seed, true)), '=');
    }
}
