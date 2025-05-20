<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\Support;

use HttpSoft\Message\ResponseTrait;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use function is_array;
use function is_string;

final class StrictResponse implements ResponseInterface
{
    use ResponseTrait {
        withHeader as traitWithHeader;
    }

    public function __construct(
        ?StreamInterface $body = null,
    ) {
        $this->init(body: $body);
    }

    public function withHeader(string $name, $value): self
    {
        if (!is_string($value) && !is_array($value)) {
            throw new LogicException('Invalid value type.');
        }
        return $this->traitWithHeader($name, $value);
    }
}
