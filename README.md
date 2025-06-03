<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px" alt="Yii">
    </a>
    <h1 align="center">Yii HTTP Middleware</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/http-middleware/v)](https://packagist.org/packages/yiisoft/http-middleware)
[![Total Downloads](https://poser.pugx.org/yiisoft/http-middleware/downloads)](https://packagist.org/packages/yiisoft/http-middleware)
[![Build status](https://github.com/yiisoft/http-middleware/actions/workflows/build.yml/badge.svg?branch=master)](https://github.com/yiisoft/http-middleware/actions/workflows/build.yml?query=branch%3Amaster)
[![Code Coverage](https://codecov.io/gh/yiisoft/http-middleware/branch/master/graph/badge.svg)](https://codecov.io/gh/yiisoft/http-middleware)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fhttp-middleware%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/http-middleware/master)
[![Static analysis](https://github.com/yiisoft/http-middleware/actions/workflows/static.yml/badge.svg?branch=master)](https://github.com/yiisoft/http-middleware/actions/workflows/static.yml?query=branch%3Amaster)
[![type-coverage](https://shepherd.dev/github/yiisoft/http-middleware/coverage.svg)](https://shepherd.dev/github/yiisoft/http-middleware)
[![psalm-level](https://shepherd.dev/github/yiisoft/http-middleware/level.svg)](https://shepherd.dev/github/yiisoft/http-middleware)

The package provides a collection of [PSR-15](https://www.php-fig.org/psr/psr-15/#12-middleware) middleware focused on
HTTP features:

- [`ContentLengthMiddleware`](docs/guide/en/content-length-middleware.md) — manages the `Content-Length` header in
  the response;
- [`CorsAllowAllMiddleware`](docs/guide/en/cors-allow-all-middleware.md) — adds
[CORS](https://developer.mozilla.org/docs/Web/HTTP/Guides/CORS) headers allowing any request origins in later
  requests;
- [`ForceSecureConnectionMiddleware`](docs/guide/en/force-secure-connection-middleware.md) — redirects insecure requests
  from HTTP to HTTPS and adds headers necessary to enhance the security policy;
- [`HeadRequestMiddleware`](docs/guide/en/head-request-middleware.md) — removes body from response for `HEAD` request;
- [`HttpCacheMiddleware`](docs/guide/en/http-cache-middleware.md) — implements HTTP caching using `Cache-Control`,
`ETag`, and `Last-Modified` headers;
- [`TagRequestMiddleware`](docs/guide/en/tag-request-middleware.md) — adds specific header to request, which can be used
for logging or debugging purposes.

For proxy related middleware, there is a separate package [Yii Proxy Middleware](https://github.com/yiisoft/proxy-middleware).

## Requirements

- PHP 8.1 or higher.

## Installation

The package could be installed with [Composer](https://getcomposer.org):

```shell
composer require yiisoft/http-middleware
```

## Documentation

- [Guide](docs/guide/en/README.md)
- [Internals](docs/internals.md)

If you need help or have a question, the [Yii Forum](https://forum.yiiframework.com/c/yii-3-0/63) is a good place
for that. You may also check out other [Yii Community Resources](https://www.yiiframework.com/community).

## License

The Yii HTTP Middleware is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
