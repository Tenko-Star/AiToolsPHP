<?php

namespace Tenko\Ai\Interfaces;

interface LogInterface
{
    public static function info(string $message, array $data = []);

    public static function warning(string $message, array $data = []);

    public static function error(string $message, array $data = []);

    public static function debug(string $message, array $data = []);
}