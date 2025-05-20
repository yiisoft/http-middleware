<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\Support;

use LogicException;
use Psr\Http\Message\StreamInterface;
use Stringable;

final class StreamStub implements StreamInterface, Stringable
{
    public function __construct(
        private readonly bool $readable = true,
    ) {
    }

    public function __toString(): string
    {
        throw new LogicException('Not implemented.');
        return '';
    }

    public function close(): void
    {
        throw new LogicException('Not implemented.');
    }

    public function detach()
    {
        throw new LogicException('Not implemented.');
    }

    public function getSize(): ?int
    {
        return null;
    }

    public function tell(): int
    {
        throw new LogicException('Not implemented.');
    }

    public function eof(): bool
    {
        return true;
    }

    public function isSeekable(): bool
    {
        return false;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        throw new LogicException('Not implemented.');
    }

    public function rewind(): void
    {
        throw new LogicException('Not implemented.');
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write(string $string): int
    {
        throw new LogicException('Not implemented.');
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        throw new LogicException('Not implemented.');
    }

    public function getContents(): string
    {
        throw new LogicException('Not implemented.');
    }

    public function getMetadata(?string $key = null)
    {
        throw new LogicException('Not implemented.');
    }
}
