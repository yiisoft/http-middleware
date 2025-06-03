# `HttpCacheMiddleware`

Middleware implements client-side caching by using standard headers like `Cache-Control`, `ETag`, and `Last-Modified`.
It optimizes responses by validating cache freshness and optionally returning `304 Not Modified` responses when content 
has not changed.

## How does it work?

Only handles `GET` and `HEAD` HTTP methods, other methods are passed through unmodified.

The middleware uses providers to obtain cache-related metadata for each request:

- `Cache-Control` header value provider (`CacheControlProviderInterface`);
- `Last-Modified` date provider (`LastModifiedProviderInterface`);
- `ETag` provider (`ETagProviderInterface`).

If the incoming request contains conditional headers (`If-None-Match` or `If-Modified-Since`), the middleware validates
the cache and may return a `304 Not Modified` response without a message body.

`ETag`, `Cache-Control`, and `Last-Modified` headers are also added to the response.

Related links:

- [ETag specification](https://datatracker.ietf.org/doc/html/rfc7232#section-2.3)
- [ETag header](https://developer.mozilla.org/docs/Web/HTTP/Reference/Headers/ETag)
- [Cache-Control header](https://developer.mozilla.org/docs/Web/HTTP/Reference/Headers/Cache-Control)
- [Last-Modified header](https://developer.mozilla.org/docs/Web/HTTP/Reference/Headers/Last-Modified)

## Default usage

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\CacheControlProviderInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\ETagProviderInterface;
use Yiisoft\HttpMiddleware\HttpCache\HttpCacheMiddleware;
use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\LastModifiedProviderInterface;

/**
 * @var ResponseFactoryInterface $responseFactory
 * @var CacheControlProviderInterface $cacheControlProvider
 * @var LastModifiedProviderInterface $lastModifiedProvider
 * @var ETagProviderInterface $eTagProvider 
 */
 
$middleware = new HttpCacheMiddleware(
    $responseFactory,
    $cacheControlProvider,
    $lastModifiedProvider,
    $eTagProvider,
);
```

## Constructor parameters

### `$responseFactory` (required)

Type: `Psr\Http\Message\ResponseFactoryInterface`

A PSR-17 response factory used to create redirect responses.

### `$cacheControlProvider`

Type: `Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\CacheControlProviderInterface`

Default: `new NullCacheControlProvider()`

An instance of `CacheControlProviderInterface` that provides the value for the `Cache-Control` header.

### `$lastModifiedProvider`

Type: `Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\LastModifiedProviderInterface`

Default: `new NullLastModifiedProvider()`

An instance of `LastModifiedProviderInterface` that provides the last modified date for the given server request.

### `$eTagProvider`

Type: `Yiisoft\HttpMiddleware\HttpCache\ETagProvider\ETagProviderInterface`

Default: `new NullETagProvider()`

An instance of `ETagProviderInterface` that provides the `ETag` instance with `ETag` metadata.

### `$eTagGenerator`

Type: `Yiisoft\HttpMiddleware\HttpCache\ETagGenerator\ETagGeneratorInterface`

Default: `new DefaultETagGenerator()`

An instance of `ETagGeneratorInterface` that generates a string `ETag` value based on the provided seed.

## `Cache-Control` header value provider

A provider should implement the `CacheControlProviderInterface` interface to supply the value of the `Cache-Control` 
header based on the incoming request.

Implementations out of the box:

- `NullCacheControlProvider` — always returns `null`, it disables cache control.
- `ConstantCacheControlProvider` — returns a predefined static cache control header value regardless of the request.
  Default is `public, max-age=3600` (value of constant `ConstantCacheControlProvider::DEFAULT_VALUE`).

## `Last-Modified` date provider

A provider should implement the `LastModifiedProviderInterface` interface to supply the last modified date for the given
server request.

Implementations out of the box:

- `NullLastModifiedProvider` — always returns `null`, it disables the last modified date.
- `PredefinedLastModifiedProvider` — provides a sequence of predefined `DateTimeImmutable` values as last modified
  dates. It is mainly intended for testing purposes. The provider returns dates one by one for each request and throws
  an exception when no more dates are available.

## `ETag` provider

A provider should implement the `ETagProviderInterface` interface to supply the `ETag` instance with `ETag` metadata.

`ETag` metadata includes seed, which is used to generate the `ETag` value, and the `weak` flag that indicates whether the
`ETag` is weak or strong. A weak `ETag` indicates that the content has not changed semantically, while a strong `ETag`
indicates that the content has not changed at all.

Implementations out of the box:

- `NullETagProvider` — always returns `null`, it disables the `ETag` header.
- `PredefinedETagProvider` — provides a sequence of predefined `ETag` instances. It is mainly intended for testing
  purposes. The provider returns `ETag` values one by one for each request and throws an exception when no more values
  are available.

## `ETag` generator

An `ETagGeneratorInterface` implementation is used to generate a string `ETag` value based on the provided seed.

Implementations out of the box:

- `DefaultETagGenerator` — generates a string `ETag` value based on the provided seed using PHP native functions 
  `base64_encode()` and `sha1()`.
- `CallableETagGenerator` — allows you to pass a callable that generates the `ETag` value based on the provided seed.
