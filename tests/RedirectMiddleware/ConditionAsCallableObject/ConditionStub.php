<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\RedirectMiddleware\ConditionAsCallableObject;

final class ConditionStub
{
    public function __construct(
        private readonly bool $result,
    ) {}

    public function __invoke(): bool
    {
        return $this->result;
    }
}
