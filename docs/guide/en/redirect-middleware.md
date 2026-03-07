# `RedirectMiddleware`

This middleware responds with a redirect to the specified URL. It doesn't pass the request to the next handler.

## General usage

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\RedirectMiddleware;

/**
 * @var ResponseFactoryInterface $responseFactory
 */

// Uses 301 (Moved Permanently) status code by default
$middleware = new RedirectMiddleware($responseFactory, 'https://example.com/new-url');

// With custom status code
$middleware = new RedirectMiddleware($responseFactory, 'https://example.com/new-url', 302);

// With a condition — redirect only POST requests
$middleware = new RedirectMiddleware(
    $responseFactory,
    'https://example.com/new-url',
    condition: static fn(ServerRequestInterface $request): bool => $request->getMethod() === 'POST',
);
```

For convenience, you can use static factory methods for common redirect status codes:

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\HttpMiddleware\RedirectMiddleware;

/**
 * @var ResponseFactoryInterface $responseFactory
 */
 
// 301 Moved Permanently
$middleware = RedirectMiddleware::permanent($responseFactory, 'https://example.com/new-url');

// 302 Found
$middleware = RedirectMiddleware::found($responseFactory, 'https://example.com/new-url');

// 303 See Other
$middleware = RedirectMiddleware::seeOther($responseFactory, 'https://example.com/new-url');

// 307 Temporary Redirect
$middleware = RedirectMiddleware::temporary($responseFactory, 'https://example.com/new-url');
```

## Conditional redirect

You can pass an optional condition callable to both the constructor and factory methods. The callable receives
the server request and returns a boolean. The redirect is performed only when the callable returns `true`.
If it returns `false`, the request is passed to the next handler:

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\HttpMiddleware\RedirectMiddleware;

/**
 * @var ResponseFactoryInterface $responseFactory
 */

// Redirect only POST requests
$middleware = new RedirectMiddleware(
    $responseFactory,
    'https://example.com/new-url',
    condition: static fn(ServerRequestInterface $request): bool => $request->getMethod() === 'POST',
);

// With a factory method
$middleware = RedirectMiddleware::permanent(
    $responseFactory,
    'https://example.com/new-url',
    static fn(ServerRequestInterface $request): bool => $request->getMethod() === 'POST',
);
```

## Constructor parameters

### `$responseFactory` (required)

Type: `Psr\Http\Message\ResponseFactoryInterface`

A PSR-17 response factory used to create the redirect response.

### `$url` (required)

Type: `string`

The URL to redirect to. It will be set as the `Location` header value.

### `$statusCode`

Type: `int`

Default: `301`

The HTTP status code for the redirect response. Common values are `301` (Moved Permanently)
and `302` (Found).

### `$condition`

Type: `callable(ServerRequestInterface):bool|null`

Default: `null`

An optional condition callable that determines whether the redirect should be performed.
The callable receives the `ServerRequestInterface` and must return a boolean:
 
- `true`, the redirect is performed;
- `false`, the request is passed to the next handler.

When `null`, the redirect is always performed.
