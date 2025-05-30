# `ContentLengthMiddleware`

A configurable middleware that manages the `Content-Length` HTTP response header. It can automatically add or remove the 
`Content-Length` header based on response characteristics and configuration options.

This middleware is used to:

- remove the `Content-Length` header if the `Transfer-Encoding` header is present (typically for chunked responses);
- add the `Content-Length` header if it's missing and the response body allows it.

Default usage:

```php
use Yiisoft\Http\Middleware\ContentLengthMiddleware;

$middleware = new ContentLengthMiddleware();
```

## Constructor parameters

### `$removeOnTransferEncoding`

Type: `boolean`

Default: `true`

Whether to remove the `Content-Length` header if the `Transfer-Encoding` header is present.

### `$add`

Type: `boolean`

Default: `true`

Whether to automatically add the `Content-Length` header if it's missing. `Content-Length` gets from the response body.

###  `$doNotAddOnStatusCode`

Type: `list<int>`

Default: 
```php
[
    100, // Continue
    101, // Switching Protocols
    102, // Processing
    204, // No Content
    205, // Reset Content
    304, // Not Modified
]
```

An array of HTTP status codes for which the `Content-Length` header should not be added.
