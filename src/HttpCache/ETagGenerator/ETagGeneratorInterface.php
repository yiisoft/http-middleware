<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\HttpCache\ETagGenerator;

interface ETagGeneratorInterface
{
    public function generate(string $seed): string;
}
