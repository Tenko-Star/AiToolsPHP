<?php

namespace Tenko\Ai;

use Tenko\Ai\AiServices\AzureService;
use Tenko\Ai\AiServices\OpenAiService;
use Tenko\Ai\Exception\AiException;
use Tenko\Ai\Exception\ConfigNotFoundException;
use Tenko\Ai\Interfaces\AiServiceInterface;

class GptServiceFactory
{
    /**
     * @return AiServiceInterface
     * @throws ConfigNotFoundException
     * @throws AiException
     */
    public static function create(): AiServiceInterface
    {
        $config = AiConfig::instance();
        $type = $config->get('gpt_type');

        switch ($type) {
            case 'open_ai':
                return new OpenAiService();

            case 'azure':
                return new AzureService();
        }

        throw new AiException('Could not found this service');
    }
}