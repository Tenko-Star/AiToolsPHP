<?php

namespace Tenko\Ai\Interfaces;

interface CacheInterface
{
    public function get(string $name, $default = null);

    public function set(string $name, $value): void;
}