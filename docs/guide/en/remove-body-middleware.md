# `RemoveBodyMiddleware`

This middleware removes the body from the response based on the status code. It is useful when you want to ensure that 
no body content is sent for certain HTTP responses, such as `204 No Content` or `304 Not Modified`.

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
