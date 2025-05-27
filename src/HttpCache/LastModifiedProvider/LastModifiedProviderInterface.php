<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider;

use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Provides last modified date given a server request.
 */
interface LastModifiedProviderInterface
{
    /**
     * Returns the last modified date for the given server request.
     *
     * @param ServerRequestInterface $request The server request for which to generate the last modified date.
     * @return DateTimeImmutable|null The last modified date or null if no last modified date is applicable.
     */
    public function get(ServerRequestInterface $request): ?DateTimeImmutable;
}
