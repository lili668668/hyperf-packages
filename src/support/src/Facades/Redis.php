<?php

declare(strict_types=1);

namespace SwooleTW\Hyperf\Support\Facades;

use Hyperf\Redis\Redis as Accessor;
use SwooleTW\Hyperf\Support\Facades\Facade;

/**
 * @mixin Accessor
 */
class Redis extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Accessor::class;
    }
}