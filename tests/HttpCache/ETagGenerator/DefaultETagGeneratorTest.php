<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\ETagGenerator;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\DefaultETagGenerator;

use function PHPUnit\Framework\assertSame;

final class DefaultETagGeneratorTest extends TestCase
{
    public function testBase(): void
    {
        $seed = 'test';
        $generator = new DefaultETagGenerator();

        $result = $generator->generate($seed);

        // Assert that the generated value is a base64-encoded SHA1 hash of the seed without `=`
        assertSame('qUqP5cyxm6YcTAhz05Hph5gvu9M', $result);
    }
}
