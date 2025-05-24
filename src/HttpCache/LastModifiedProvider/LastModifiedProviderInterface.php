<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider;

use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;

interface LastModifiedProviderInterface
{
    public function get(ServerRequestInterface $request): ?DateTimeImmutable;
}
