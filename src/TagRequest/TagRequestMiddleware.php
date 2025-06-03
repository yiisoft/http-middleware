<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\TagRequest;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\HttpMiddleware\TagRequest\TagProvider\TagProviderInterface;
use Yiisoft\HttpMiddleware\TagRequest\TagProvider\TimeBasedTagProvider;

/**
 * Tags request with a value that could be later used for identifying it.
 */
final readonly class TagRequestMiddleware implements MiddlewareInterface
{
    /**
     * @param string $attributeName The name of the attribute to store the tag in.
     * @param TagProviderInterface $tagProvider The tag provider.
     */
    public function __construct(
        private string $attributeName = 'requestTag',
        private TagProviderInterface $tagProvider = new TimeBasedTagProvider(),
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
