# `CorsAllowAllMiddleware`

A middleware that adds permissive [CORS](https://developer.mozilla.org/docs/Web/HTTP/Guides/CORS) 
(Cross-Origin Resource Sharing) headers to all HTTP responses. It allows all origins, headers, and credentials, 
making it suitable for development or internal APIs or publicly available APIs.

> ⚠️ **Security notice**
> 
> This middleware should **not be used in production** as-is unless you're absolutely certain it's safe for your
> context. Allowing all origins and credentials without restriction poses a **serious security risk**.

`CorsAllowAllMiddleware` is used to:

- allow requests from any origin;
- allow all HTTP methods and headers;
- expose all response headers to the client;
- enable credentials (cookies, authorization headers, etc.) for cross-origin requests;
- set a cache lifetime for preflight responses.

General usage:

```php
use Yiisoft\HttpMiddleware\CorsAllowAllMiddleware;

$middleware = new CorsAllowAllMiddleware();
```

To handle CORS preflight requests without passing them to the request handler, provide a PSR-17 response factory:

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\HttpMiddleware\CorsAllowAllMiddleware;

/** @var ResponseFactoryInterface $responseFactory */
$middleware = new CorsAllowAllMiddleware($responseFactory);
```

The constructor argument is optional for backward compatibility. Without a response factory, preflight requests are
passed to the next request handler and CORS headers are added to its response.
