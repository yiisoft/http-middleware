# `HeadRequestMiddleware`
```php
use Psr\Http\Message\StreamFactoryInterface;
use Yiisoft\HttpMiddleware\HeadRequestMiddleware;

/**
 * @var StreamFactoryInterface $streamFactory 
 */

$middleware = new HeadRequestMiddleware($streamFactory);
```
