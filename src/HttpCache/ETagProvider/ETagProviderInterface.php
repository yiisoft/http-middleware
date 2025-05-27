<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagProvider;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETag;

/**
 * Obtains {@see ETag} for a given server request.
 */
interface ETagProviderInterface
{
    /**
     * Returns an {@see ETag} instance for the given server request.
     *
     * @param ServerRequestInterface $request The server request for which to generate the ETag.
     * @return ETag|null Instance of {@see ETag} or null if no ETag can be generated for the request.
     */
    public function get(ServerRequestInterface $request): ?ETag;
}
