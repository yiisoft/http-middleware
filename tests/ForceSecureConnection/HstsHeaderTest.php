<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\ForceSecureConnection;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\ForceSecureConnection\HstsHeader;

use function PHPUnit\Framework\assertSame;

final class HstsHeaderTest extends TestCase
{
    public function testBase(): void
    {
        $header = new HstsHeader();

        assertSame('max-age=31536000', $header->getValue());
    }

    public function testCustomValues(): void
    {
        $header = new HstsHeader(500, true);

        assertSame('max-age=500; includeSubDomains', $header->getValue());
    }
}
