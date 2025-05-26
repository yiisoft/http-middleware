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

    public function rawValue(ETagGeneratorInterface $generator): string
    {
        return $this->value ??= $generator->generate($this->seed);
    }

    public function headerValue(ETagGeneratorInterface $generator): string
    {
        $value = '"' . $this->rawValue($generator) . '"';
        if ($this->weak) {
            $value = 'W/' . $value;
        }
        return $value;
    }
}
