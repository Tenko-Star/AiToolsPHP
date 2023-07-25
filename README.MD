# Ai Tools PHP

This is a Util for ChatGPT and Azure.

## Quick Start

- ChatGPT:

```php
<?php

\Tenko\Ai\AiConfig::init([
    'gpt_type' => 'open_ai',
    // or 'gpt_type' => 'azure'
    'api_key' => ['xxx', 'yyy', 'zzz'],
    
    // if azure
    // 'azure_resource_name' => '',
    // 'azure_deployments' => '',
    // 'azure_api_version' => ''
]);

$service = \Tenko\Ai\GptServiceFactory::create();

$response = $service->chat([
    'messages' => [
        ['role' => 'system', 'content' => 'You are an artificial intelligence assistant, and your responsibility is to help users solve problems.'],
        ['role' => 'user', 'content' => 'Hi.'],
    ]
]);

var_dump($response);

```

For more details see [configs.md](./doc/configs.md)