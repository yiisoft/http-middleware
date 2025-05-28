# `ForceSecureConnectionMiddleware`

```php
use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\HttpMiddleware\ForceSecureConnection\ForceSecureConnectionMiddleware;

/**
 * @var ResponseFactoryInterface $responseFactory 
 */

$middleware = new ForceSecureConnectionMiddleware($responseFactory);
```
