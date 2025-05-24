<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\CacheControlProvider;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\NullCacheControlProvider;

use function PHPUnit\Framework\assertNull;

final class NullCacheControlProviderTest extends TestCase
{
    public function testGetReturnsNull(): void
    {
        $request = new ServerRequest();
        $provider = new NullCacheControlProvider();

        $result = $provider->get($request);

        assertNull($result);
    }
}
