<?php

namespace Tenko\Ai\Library;

use Tenko\Ai\Interfaces\LogInterface;

/**
 * !! THIS IS NOT A LOGGER
 * !! DO NOT USE THIS
 */
class LogStub implements LogInterface
{

    public static function info(string $message, array $data)
    {
        return;
    }

    public static function warning(string $message, array $data)
    {
        return;
    }

    public static function error(string $message, array $data)
    {
        return;
    }

    public static function debug(string $message, array $data)
    {
        return;
    }
}