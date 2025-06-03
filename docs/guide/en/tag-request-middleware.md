# `TagRequestMiddleware`

Middleware tags requests with a unique identifier. This tag can be used later during the request lifecycle to identify, 
track, or log the request for debugging, correlation, or caching purposes.

## General usage

The middleware generates the tag using a configurable `TagProviderInterface` implementation and attaches it to
the request as an attribute.

```php
use Yiisoft\HttpMiddleware\TagRequest\TagRequestMiddleware;

$middleware = new TagRequestMiddleware();
```

## Constructor parameters

### `$attributeName`

Type: `string`

Default: `'requestTag'`

The name of the attribute where the tag will be stored in the request.

### `$tagProvider`

Type: `Yiisoft\HttpMiddleware\TagRequest\TagProvider\TagProviderInterface`

Default: `new TimeBasedTagProvider()`

An instance of `TagProviderInterface` that provides the tag value.

## Tag providers

A provider should implement the `TagProviderInterface` interface to supply the tag.

Implementations out of the box:

- `TimeBasedTagProvider` — generates a unique tag based on the current time and additional entropy.
- `PredefinedTagProvider` — provides a sequence of predefined tags. It is mainly intended for testing purposes.
  The provider returns tags one by one for each request and throws an exception when no more dates are available.
- `PrefixedTagProvider` — a tag provider decorator that prefixes the result of a decorated provider with a specified
  string.
