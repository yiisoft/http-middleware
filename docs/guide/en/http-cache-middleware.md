# `HttpCacheMiddleware`

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\HttpMiddleware\HttpCache\CacheControlProvider\CacheControlProviderInterface;
use Yiisoft\HttpMiddleware\HttpCache\ETagProvider\ETagProviderInterface;
use Yiisoft\HttpMiddleware\HttpCache\HttpCacheMiddleware;
use Yiisoft\HttpMiddleware\HttpCache\LastModifiedProvider\LastModifiedProviderInterface;

/**
 * @var ResponseFactoryInterface $responseFactory
 * @var CacheControlProviderInterface $cacheControlProvider
 * @var LastModifiedProviderInterface $lastModifiedProvider
 * @var ETagProviderInterface $eTagProvider 
 */
 
$middleware = new HttpCacheMiddleware(
    $responseFactory,
    $cacheControlProvider,
    $lastModifiedProvider,
    $eTagProvider,
);
```
