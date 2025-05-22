<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

use function uniqid;

final class TimeBasedTagProvider implements TagProviderInterface
{
    public function get(): string
    {
        return uniqid(more_entropy: true);
    }
}
