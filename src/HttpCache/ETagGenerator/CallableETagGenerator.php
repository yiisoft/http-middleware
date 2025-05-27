<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagGenerator;

/**
 * Generates a string ETag value using a callable function.
 *
 * @psalm-type TCallable = callable(string): string
 */
final class CallableETagGenerator implements ETagGeneratorInterface
{
    /**
     * @var callable
     * @psalm-var TCallable
     */
    private $callable;

    /**
     * @param callable $callable A callable function that takes a string seed and returns a string ETag value.
     *
     * @psalm-param TCallable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function generate(string $seed): string
    {
        return ($this->callable)($seed);
    }
}
