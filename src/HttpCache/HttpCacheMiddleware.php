<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache;

use DateTimeImmutable;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\CacheControlProviderInterface;
use Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\NullCacheControlProvider;
use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\DefaultETagGenerator;
use Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\ETagGeneratorInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\ETagProviderInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\NullETagProvider;
use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\LastModifiedProviderInterface;
use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\NullLastModifiedProvider;

use function array_map;
use function in_array;

/**
 * Middleware that implements HTTP caching using `Cache-Control`, `ETag`, and `Last-Modified` headers.
 */
final readonly class HttpCacheMiddleware implements MiddlewareInterface
{
    /**
     * @param ResponseFactoryInterface $responseFactory The response factory to create responses.
     * @param CacheControlProviderInterface $cacheControlProvider The cache control headers provider.
     * @param LastModifiedProviderInterface $lastModifiedProvider The last modified dates provider.
     * @param ETagProviderInterface $eTagProvider The provider for {@see ETag}.
     * @param ETagGeneratorInterface $eTagGenerator The {@see ETag} string values generator.
     */
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private CacheControlProviderInterface $cacheControlProvider = new NullCacheControlProvider(),
        private LastModifiedProviderInterface $lastModifiedProvider = new NullLastModifiedProvider(),
        private ETagProviderInterface $eTagProvider = new NullETagProvider(),
        private ETagGeneratorInterface $eTagGenerator = new DefaultETagGenerator(),
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return $handler->handle($request);
        }

        $cacheControl = $this->cacheControlProvider->get($request);
        $eTag = $this->eTagProvider->get($request);
        $eTagHeader = $eTag === null ? null : new ETagHeader($eTag, $this->eTagGenerator);
        $lastModified = $this->lastModifiedProvider->get($request);

        $cacheIsValid = $this->validateCache($request, $lastModified, $eTagHeader);

        if ($cacheIsValid) {
            $response = $this->responseFactory->createResponse(304);
            $response = $this->addETagHeader($response, $eTagHeader);
            $response = $this->addCacheControlHeader($response, $cacheControl);
            if ($eTagHeader === null) {
                $response = $this->addLastModifiedHeader($response, $lastModified);
            }
            return $response;
        }

        $response = $handler->handle($request);
        $response = $this->addETagHeader($response, $eTagHeader);
        $response = $this->addCacheControlHeader($response, $cacheControl);
        return $this->addLastModifiedHeader($response, $lastModified);
    }

    private function validateCache(
        ServerRequestInterface $request,
        ?DateTimeImmutable $lastModified,
        ?ETagHeader $eTagHeader,
    ): bool
    {
        if ($request->hasHeader('If-None-Match')) {
            if ($eTagHeader === null) {
                return false;
            }

            $headerETags = $this->extractRawETagValues($request);
            if ($headerETags === []) {
                return false;
            }
            return in_array($eTagHeader->rawValue(), $headerETags, true);
        }

        if ($request->hasHeader('If-Modified-Since')) {
            if ($lastModified === null) {
                return false;
            }

            $ifModifiedSince = @strtotime($request->getHeaderLine('If-Modified-Since'));
            if ($ifModifiedSince === false) {
                return false;
            }

            return $ifModifiedSince >= $lastModified->getTimestamp();
        }

        return false;
    }

    private function addCacheControlHeader(ResponseInterface $response, ?string $value): ResponseInterface
    {
        if ($value === null) {
            return $response;
        }

        return $response->withHeader('Cache-Control', $value);
    }

    private function addETagHeader(ResponseInterface $response, ?ETagHeader $eTagHeader): ResponseInterface
    {
        if ($eTagHeader === null) {
            return $response;
        }

        return $response->withHeader('ETag', $eTagHeader->headerValue());
    }

    private function addLastModifiedHeader(ResponseInterface $response, ?DateTimeImmutable $date): ResponseInterface
    {
        if ($date === null) {
            return $response;
        }

        return $response->withHeader(
            'Last-Modified',
            gmdate('D, d M Y H:i:s', $date->getTimestamp()) . ' GMT',
        );
    }

    /**
     * @psalm-return list<string>
     */
    private function extractRawETagValues(ServerRequestInterface $request): array
    {
        $rawValue = $request->getHeaderLine('If-None-Match');
        if ($rawValue === '') {
            return [];
        }

        return array_map(
            static function (string $value): string {
                /**
                 * @var string We use a correct pattern, so `preg_replace` always returns a string.
                 */
                return preg_replace('~^\s*(?:W/)?"([^"]+)"\s*$~', '$1', $value);
            },
            explode(',', $rawValue),
        );
    }
}
