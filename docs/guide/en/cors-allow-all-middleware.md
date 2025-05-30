# `CorsAllowAllMiddleware`

A middleware that adds permissive [CORS](https://developer.mozilla.org/docs/Web/HTTP/Guides/CORS) 
(Cross-Origin Resource Sharing) headers to all HTTP responses. It allows all origins, headers, and credentials, 
making it suitable for development or internal APIs.

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

There are no constructor arguments, the middleware is ready to use out of the box.
