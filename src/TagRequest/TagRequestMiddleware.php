<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Tags request with a random value that could be later used for identifying it.
 */
final class TagRequestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly string $attributeName = 'requestTag',
        private readonly TagProviderInterface $tagProvider = new TimeBasedTagProvider(),
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request->withAttribute(
            $this->attributeName,
            $this->tagProvider->get(),
        );

        return $handler->handle($request);
    }
}
