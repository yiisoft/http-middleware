<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\TagRequest;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\TagRequest\TimeBasedTagProvider;

use function PHPUnit\Framework\assertMatchesRegularExpression;

final class TimeBasedTagProviderTest extends TestCase
{
    public function testBase(): void
    {
        $provider = new TimeBasedTagProvider();

        $regex = '/^[0-9a-f]+\.[0-9]+$/';
        assertMatchesRegularExpression($regex, $provider->get());
        assertMatchesRegularExpression($regex, $provider->get());
        assertMatchesRegularExpression($regex, $provider->get());
    }
}
