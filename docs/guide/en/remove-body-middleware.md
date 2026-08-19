# `RemoveBodyMiddleware`

This middleware removes the body from the response based on the status code. It is useful when you want to ensure that 
no body content is sent for certain HTTP responses, such as `204 No Content` or `304 Not Modified`.

When the body is removed, headers describing it — `Content-Length` and `Transfer-Encoding` by default — are removed
as well, so the response does not advertise content that is no longer there. The exception is `304 Not Modified`:
per [RFC 9110, §8.6](https://datatracker.ietf.org/doc/html/rfc9110#section-8.6) and
[RFC 9112, §6.1](https://datatracker.ietf.org/doc/html/rfc9112#section-6.1), a `304` response may still carry these
headers to describe the representation that would have been sent in a `200 OK` response to the same request, so
they are kept by default.

General usage:

```php
use Psr\Http\Message\StreamFactoryInterface;
use Yiisoft\HttpMiddleware\RemoveBodyMiddleware;

/**
 * @var StreamFactoryInterface $streamFactory 
 */

$middleware = new RemoveBodyMiddleware($streamFactory);
```

## Constructor parameters

### `$streamFactory` (required)

Type: `Psr\Http\Message\StreamFactoryInterface`

A PSR-17 stream factory used to create an empty body.

###  `$statusCodes`

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

An array of HTTP status codes for which the body should be removed.

### `$keepHeadersOnStatusCode`

Type: `list<int>`

Default:
```php
[
    304, // Not Modified
]
```

An array of HTTP status codes for which headers listed in `$removedHeaders` are kept even though the body is
removed. By default, this only applies to `304 Not Modified`, since per
[RFC 9110, §8.6](https://datatracker.ietf.org/doc/html/rfc9110#section-8.6)
and [RFC 9112, §6.1](https://datatracker.ietf.org/doc/html/rfc9112#section-6.1) a `304` response may still carry
`Content-Length` and `Transfer-Encoding` describing the representation that would have been sent in a `200 OK`
response to the same request.

### `$removedHeaders`

Type: `list<non-empty-string>`

Default:
```php
[
    'Content-Length',
    'Transfer-Encoding',
]
```

An array of headers to remove together with the body, for status codes not listed in `$keepHeadersOnStatusCode`.
