<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\Tests\Support;

use HttpSoft\Message\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class FakeRequestHandler implements RequestHandlerInterface
{
    private ?ServerRequestInterface $request = null;

    public function __construct(
        public readonly ResponseInterface $response = new Response(),
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return $this->response;
    }

    public function getLastRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }
}
