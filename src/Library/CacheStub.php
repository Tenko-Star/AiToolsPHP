<?php

namespace Tenko\Ai\Library;

use Tenko\Ai\Interfaces\CacheInterface;

class CacheStub implements CacheInterface
{

    public static function get(string $name, $default = null)
    {
        return $default;
    }

    public static function set(string $name, $value): void
    {
        return;
    }
}