<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache;

use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\ETagGeneratorInterface;

final class ETag
{
    private ?string $value = null;

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
