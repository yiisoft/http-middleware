<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache;

use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\ETagGeneratorInterface;

/**
 * @internal
 */
final class ETagHeader
{
    private ?string $value = null;

    public function __construct(
        private readonly ETag $eTag,
        private readonly ETagGeneratorInterface $generator,
    ) {
    }

    /**
     * Returns the raw ETag value generated from the seed.
     *
     * @return string The raw ETag value generated from the seed.
     */
    public function rawValue(): string
    {
        return $this->value ??= $this->generator->generate($this->eTag->seed);
    }

    /**
     * Returns the ETag value formatted for use in HTTP headers.
     *
     * The value is enclosed in double quotes and prefixed with 'W/' if the ETag is weak.
     *
     * @return string The formatted ETag header value.
     */
    public function headerValue(): string
    {
        $value = '"' . $this->rawValue() . '"';
        if ($this->eTag->weak) {
            $value = 'W/' . $value;
        }
        return $value;
    }
}
