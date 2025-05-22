<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

interface TagProviderInterface
{
    public function get(): string;
}
