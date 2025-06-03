<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest\TagProvider;

use function uniqid;

/**
 * Represents a tag provider that generates a unique tag based on the current time and additional entropy.
 *
 * @see https://www.php.net/manual/function.uniqid.php
 */
final class TimeBasedTagProvider implements TagProviderInterface
{
    public function get(): string
    {
        return uniqid(more_entropy: true);
    }
}
