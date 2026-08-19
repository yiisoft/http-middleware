# Yii HTTP Middleware Change Log

## 1.2.2 under development

- New #29: Add `$keepHeadersOnStatusCode` and `$removedHeaders` constructor parameters to `RemoveBodyMiddleware` (@vjik)
- New #29: Add `$removeOnStatusCode` constructor parameter to `ContentLengthMiddleware` (@vjik)
- Bug #29: Remove `Content-Length` and `Transfer-Encoding` headers in `RemoveBodyMiddleware` when the body is
  removed (@vjik)
- Bug #29: Remove already present `Content-Length` header in `ContentLengthMiddleware` for status codes that must
  not carry one (@vjik)

## 1.2.1 August 10, 2026

- Bug #24: Fix invalid CORS headers and add optional preflight request handling to `CorsAllowAllMiddleware` (@samdark)

## 1.2.0 March 10, 2026

- New #17: Add `RedirectMiddleware` (@vjik)
- Chg #19: Change PHP constraint in `composer.json` to `8.1 - 8.5` (@vjik)

## 1.1.0 June 09, 2025

- New #10: Add `RemoveBodyMiddleware` (@vjik)

## 1.0.0 June 04, 2025

- Initial release.
