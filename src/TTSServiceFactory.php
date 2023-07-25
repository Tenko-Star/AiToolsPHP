<?php

namespace Tenko\Ai;

use app\common\exception\TTSException;
use app\common\service\tts\AzureTTSService;

class TTSServiceFactory
{
    public static function create(string $engine, array $config): TTSServiceInterface
    {
        switch ($engine) {
            case 'azure':
                $engine = new AzureTTSService($config);
                break;

            default:
                throw new TTSException('Could not found this engine');
        }

        return $engine;
    }
}