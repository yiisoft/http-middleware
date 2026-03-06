# `RedirectMiddleware`

This middleware responds with a redirect to the specified URL. It doesn't pass the request to the next handler.

General usage:

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\HttpMiddleware\RedirectMiddleware;

/**
 * @var ResponseFactoryInterface $responseFactory
 */

// Uses 301 (Moved Permanently) status code by default
$middleware = new RedirectMiddleware($responseFactory, 'https://example.com/new-url');

// With custom status code
$middleware = new RedirectMiddleware($responseFactory, 'https://example.com/new-url', 302);
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
