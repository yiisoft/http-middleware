<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagGenerator;

/**
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
