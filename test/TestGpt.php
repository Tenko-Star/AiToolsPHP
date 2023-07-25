<?php

namespace Tenko\Test;

use PHPUnit\Framework\TestCase;
use Tenko\Ai\AiConfig;
use Tenko\Ai\GptServiceFactory;
use Tenko\Ai\Interfaces\CacheInterface;

class TestGpt extends TestCase
{
    public function testRequest() {
        $env = require '.env';

        AiConfig::init([
            'gpt_type' => 'open_ai',
            'api_key' => $env['api_key']
        ]);

        AiConfig::setCache(new class implements CacheInterface {

            public function get(string $name, $default = null)
            {
                if (!file_exists("./$name.cache")) {
                    return $default;
                }

                return unserialize(file_get_contents("./$name.cache"));
            }

            public function set(string $name, $value): void
            {
                file_put_contents("./$name.cache", serialize($value));
            }
        });

        $service = GptServiceFactory::create();
        $response = $service->chat([
            'messages' => [
                ['role' => 'system', 'content' => 'You are an artificial intelligence assistant, and your responsibility is to help users solve problems.'],
                ['role' => 'user', 'content' => 'Hi.'],
            ]
        ]);

        $this->assertNotEmpty($response);

        var_dump($response);
    }
}
