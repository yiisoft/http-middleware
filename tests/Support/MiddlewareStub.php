<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\Support;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MiddlewareStub implements MiddlewareInterface
{
    public function __construct(
        private readonly ?ResponseInterface $response = null,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->response ?? $handler->handle($request);
    }
}
