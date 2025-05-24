<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider;

use Psr\Http\Message\ServerRequestInterface;

interface CacheControlProviderInterface
{
    public function get(ServerRequestInterface $request): ?string;
}
