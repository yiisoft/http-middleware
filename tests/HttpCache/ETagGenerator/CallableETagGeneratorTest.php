<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\HttpCache\ETagGenerator;

use PHPUnit\Framework\TestCase;
use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\CallableETagGenerator;

use function PHPUnit\Framework\assertSame;

final class CallableETagGeneratorTest extends TestCase
{
    public function testBase(): void
    {
        $generator = new CallableETagGenerator(
            static fn(string $seed): string => 'stub-' . $seed,
        );

        $result = $generator->generate('test');

        assertSame('stub-test', $result);
    }
}
