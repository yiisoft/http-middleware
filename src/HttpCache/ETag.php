<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache;

use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\ETagGeneratorInterface;

/**
 * Represents an ETag (Entity Tag) used for HTTP caching.
 *
 * An ETag is a unique identifier assigned to a specific version of a resource.
 * It is used by clients to determine if the resource has changed since the last request.
 */
final class ETag
{
    private ?string $value = null;

    /**
     * @param string $seed The seed value used to generate the ETag.
     * @param bool $weak Indicates whether the ETag is weak (if the content is semantically equal, but not byte-equal).
     */
    public function __construct(
        public readonly string $seed,
        public readonly bool $weak = false,
    ) {
    }

    /**
     * Returns the raw ETag value generated from the seed.
     *
     * @param ETagGeneratorInterface $generator The generator used to create the ETag value.
     * @return string The raw ETag value generated from the seed.
     */
    public function rawValue(ETagGeneratorInterface $generator): string
    {
        return $this->value ??= $generator->generate($this->seed);
    }

    /**
     * Returns the ETag value formatted for use in HTTP headers.
     *
     * The value is enclosed in double quotes and prefixed with 'W/' if the ETag is weak.
     *
     * @param ETagGeneratorInterface $generator The generator used to create the ETag value.
     * @return string The formatted ETag header value.
     */
    public function headerValue(ETagGeneratorInterface $generator): string
    {
        $value = '"' . $this->rawValue($generator) . '"';
        if ($this->weak) {
            $value = 'W/' . $value;
        }
        return $value;
    }
}
