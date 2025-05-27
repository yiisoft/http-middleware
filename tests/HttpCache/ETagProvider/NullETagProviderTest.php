<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\ETagProvider;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\NullETagProvider;

use function PHPUnit\Framework\assertNull;

final class NullETagProviderTest extends TestCase
{
    public function testBase(): void
    {
        $provider = new NullETagProvider();

        $result = $provider->get(new ServerRequest());

        assertNull($result);
    }
}
