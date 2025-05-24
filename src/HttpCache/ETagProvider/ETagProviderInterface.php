<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagProvider;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETag;

interface ETagProviderInterface
{
    public function get(ServerRequestInterface $request): ?ETag;
}
