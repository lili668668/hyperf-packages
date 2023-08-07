<?php

declare(strict_types=1);

namespace SwooleTW\Hyperf\Cookie;

use SwooleTW\Hyperf\Cookie\Contracts\Cookie as CookieContract;
use SwooleTW\Hyperf\Cookie\CookieManager;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                CookieContract::class => CookieManager::class,
            ],
        ];
    }
}