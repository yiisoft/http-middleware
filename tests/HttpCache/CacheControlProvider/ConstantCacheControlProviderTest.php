<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\CacheControlProvider;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\ConstantCacheControlProvider;

use function PHPUnit\Framework\assertSame;

final class ConstantCacheControlProviderTest extends TestCase
{
    public function testDefault(): void
    {
        $request = new ServerRequest();
        $provider = new ConstantCacheControlProvider();

        $result = $provider->get($request);

        assertSame(ConstantCacheControlProvider::DEFAULT_VALUE, $result);
    }

    public function testCustomValue(): void
    {
        $request = new ServerRequest();
        $provider = new ConstantCacheControlProvider('public, max-age=7200');

        $result = $provider->get($request);

        assertSame('public, max-age=7200', $result);
    }
}
