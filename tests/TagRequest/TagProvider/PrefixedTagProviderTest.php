<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\TagRequest\TagProvider;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\TagRequest\TagProvider\PredefinedTagProvider;
use Yiisoft\HttpMiddleware\TagRequest\TagProvider\PrefixedTagProvider;

use function PHPUnit\Framework\assertSame;

final class PrefixedTagProviderTest extends TestCase
{
    public function testBase(): void
    {
        $provider = new PrefixedTagProvider(
            'test_',
            new PredefinedTagProvider(['tag1', 'tag2']),
        );

        assertSame('test_tag1', $provider->get());
        assertSame('test_tag2', $provider->get());
    }
}
