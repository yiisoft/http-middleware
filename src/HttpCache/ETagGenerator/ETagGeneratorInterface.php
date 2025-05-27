<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagGenerator;

/**
 * Interface defines a method to generate a string ETag value based on a given seed string.
 */
interface ETagGeneratorInterface
{
    /**
     * Generates a string ETag value based on the provided seed.
     *
     * @param string $seed The seed value used to generate the ETag.
     * @return string The generated string ETag value.
     */
    public function generate(string $seed): string;
}
