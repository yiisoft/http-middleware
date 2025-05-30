# `ForceSecureConnectionMiddleware`

This middleware redirects insecure HTTP requests to HTTPS and adds security-related headers to enhance your 
application’s security.

> It's recommended to enforce HTTPS at the web server level (e.g., Nginx, Angie, Apache) when possible. Use this
> middleware if you can't configure the server or are building a portable product.

Default usage:

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\HttpMiddleware\ForceSecureConnection\ForceSecureConnectionMiddleware;

/**
 * @var ResponseFactoryInterface $responseFactory 
 */

$middleware = new ForceSecureConnectionMiddleware($responseFactory);
```

## Constructor parameters

### `$responseFactory` (required)

Type: `Psr\Http\Message\ResponseFactoryInterface`

A PSR-17 response factory used to create redirect responses.

### `$redirectOptions`

Type: `Yiisoft\HttpMiddleware\ForceSecureConnection\RedirectOptions`

Default: `new RedirectOptions()`

An instance of `RedirectOptions` that allows you to customize the redirect behavior:

- `enabled` - whether to enable the redirect from HTTP to HTTPS. Default is `true`.
- `port` - the port to which the redirect should occur. Default is `null`, meaning the port will not be added to the
  URL.

Examples:

```php
use Yiisoft\HttpMiddleware\ForceSecureConnection\RedirectOptions;

// Redirect to HTTPS on port 443
new RedirectOptions(port: 443);

// Disable the redirect
new RedirectOptions(enabled: false);
```

### `$cspHeader`

Type: `string|null`

Default: `upgrade-insecure-requests; default-src https:`
(value of constant `ForceSecureConnectionMiddleware::DEFAULT_CSP_HEADER`)

Value for the `Content-Security-Policy` header, which instructs the browser to upgrade insecure requests to secure ones
and only allow resources from secure origins. Set to `null` to disable this header.

### `$hstsHeader`

Type: `Yiisoft\HttpMiddleware\ForceSecureConnection\HstsHeader|null`

Default: `new HstsHeader()`

An instance of `HstsHeader` that represents the HTTP `Strict-Transport-Security` (HSTS) header:

- `maxAge` - the time in seconds that the browser should remember to only access the site via HTTPS.
  Default is one year — `31_536_000` (value of constant `HstsHeader::DEFAULT_MAX_AGE`).
- `includeSubDomains` - whether to include subdomains in the HSTS policy. Default is `false`.

This header tells the browser that your site works with HTTPS only. Set to `null` to disable this header.

Examples:

```php
use Yiisoft\HttpMiddleware\ForceSecureConnection\HstsHeader;

// HSTS header with a max age of 6 months
new HstsHeader(maxAge: 15_768_000);

// HSTS header with include subdomains
new HstsHeader(subdomains: true);
```
