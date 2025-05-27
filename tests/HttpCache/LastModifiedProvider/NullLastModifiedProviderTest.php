<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\LastModifiedProvider;

use HttpSoft\Message\ServerRequest;
use PHPUnit\Framework\TestCase;

use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\NullLastModifiedProvider;

use function PHPUnit\Framework\assertNull;

final class NullLastModifiedProviderTest extends TestCase
{

    public function testBase(): void
    {
        $provider = new NullLastModifiedProvider();

        $result = $provider->get(new ServerRequest());

        assertNull($result);
    }
}
