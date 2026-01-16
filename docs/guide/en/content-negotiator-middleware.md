# `ContentNegotiatorMiddleware`

A middleware that performs [content negotiation](https://developer.mozilla.org/en-US/docs/Web/HTTP/Content_negotiation)
by delegating request handling to specific middlewares based on the `Accept` header. This allows different request
processing pipelines based on the client's preferred content type.

The middleware is useful when you need to handle different content types (JSON, XML, HTML, etc.) with different
processing logic or formatters.

General usage:

```php
use Yiisoft\HttpMiddleware\ContentNegotiatorMiddleware;

$middleware = new ContentNegotiatorMiddleware([
    'application/json' => $jsonMiddleware,
    'application/xml' => $xmlMiddleware,
    'text/html' => $htmlMiddleware,
]);
```

## How it works

1. The middleware examines the `Accept` header from the incoming request and sorts the accepted types by their quality
   values (q-parameter), with higher quality values taking precedence.
2. It iterates through the sorted accept types (highest quality first).
3. For each accept type, it checks all configured middlewares to find a matching content type using substring matching.
4. When it finds a match, it delegates the request to the corresponding middleware.
5. If no match is found, the request is passed to the next handler in the pipeline without any special processing.

## Constructor parameters

### `$middlewares` (required)

Type: `array<string, MiddlewareInterface>`

A map of content types to middleware instances. The array key is the content type string (e.g., `'application/json'`),
and the value is an instance of `Psr\Http\Server\MiddlewareInterface`.

Example:

```php
use Yiisoft\HttpMiddleware\ContentNegotiatorMiddleware;

$middleware = new ContentNegotiatorMiddleware([
    'application/json' => new JsonFormatterMiddleware(),
    'application/xml' => new XmlFormatterMiddleware(),
    'text/html' => new HtmlFormatterMiddleware(),
]);
```

## Usage examples

### Basic content negotiation

```php
use Yiisoft\HttpMiddleware\ContentNegotiatorMiddleware;

$middleware = new ContentNegotiatorMiddleware([
    'application/json' => new JsonResponseMiddleware(),
    'application/xml' => new XmlResponseMiddleware(),
]);

// Request with Accept: application/json -> JsonResponseMiddleware will be used
// Request with Accept: application/xml -> XmlResponseMiddleware will be used
// Request with Accept: text/html -> passed to next handler (no match)
```

### Handling multiple accept values

When a client sends multiple content types in the `Accept` header (e.g., `Accept: text/html, application/json;q=0.9`),
the middleware sorts them by quality values and processes them in order of preference. Higher quality values are processed
first.

```php
// Request with Accept: text/html, application/json;q=0.9
// Will use HtmlFormatterMiddleware (text/html has default q=1.0, higher than json's q=0.9)

// Request with Accept: application/json;q=0.5, application/xml;q=0.9
// Will use XmlFormatterMiddleware (xml has higher quality value)

// Request with Accept: application/json, application/xml
// Will use JsonFormatterMiddleware (both have default q=1.0, first in Accept header wins)
```

### Content type with parameters

The middleware uses substring matching, so it works with content types that have additional parameters:

```php
// Request with Accept: application/json;charset=UTF-8
// Will match 'application/json' and use JsonResponseMiddleware
```

## Validation

The middleware validates the constructor parameters and throws a `RuntimeException` if:

- A content type key is not a string
- A middleware value is not an instance of `MiddlewareInterface`

```php
// This will throw RuntimeException
new ContentNegotiatorMiddleware([
    123 => $middleware, // Invalid: key must be a string
]);

// This will also throw RuntimeException
new ContentNegotiatorMiddleware([
    'application/json' => 'invalid', // Invalid: value must be MiddlewareInterface
]);
```
