# `HeadRequestMiddleware`

This middleware ensures that HTTP `HEAD` requests return a response without a body, as required by the HTTP
specification.

General usage:

```php
use Psr\Http\Message\StreamFactoryInterface;
use Yiisoft\HttpMiddleware\HeadRequestMiddleware;

/**
 * @var StreamFactoryInterface $streamFactory 
 */

$middleware = new HeadRequestMiddleware($streamFactory);
```

## Constructor parameters

### `$streamFactory` (required)

Type: `Psr\Http\Message\StreamFactoryInterface`

A PSR-17 stream factory used to create an empty body.
