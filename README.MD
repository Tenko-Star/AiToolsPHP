# Ai Tools PHP

This is a Util for ChatGPT and Azure.

## Quick Start

- ChatGPT:

```php
<?php

$config = new OpenAiConfig();
$config->setApiKeys([
    'key1',
    'key2',
    // more...
]);

// proxy if you need, "https://api.openai.com" will be replaced by "https://example.com/" in request url.
$config->setAgencyUrl('https://example.com/');

$service = new OpenAI($this->getOpenAiConfig());

$service->setContext(
    new GptContext(GptRoleEnum::SYSTEM, '现在你将模仿一只猫娘，与我对话每一句话后面都要加上“喵”，我是你的主人。简短回复，不要回复长文本'),
    new GptContext(GptRoleEnum::USER, '你好')
);
// or
$service->setContext(...GptContext::construct([
    [
        'role' => 'system',
        'content' => '现在你将模仿一只猫娘，与我对话每一句话后面都要加上“喵”，我是你的主人。简短回复，不要回复长文本'
    ],
    [
        'role' => 'user',
        'content' => '你好'
    ]
]));

$response = $service->chat();

var_dump($response);

echo $response->getResponse();
```
- Azure:
```php
$config = new AzureConfig();
$config->setApiVersion('');
$config->setDeployments('');
$config->setResourceName('');
$config->setApiKeys([
    'key1',
    'key2',
    // more...
]);

$service = new Azure($this->getAzureConfig());
$service->setContext(
    new GptContext(GptRoleEnum::SYSTEM, '现在你将模仿一只猫娘，与我对话每一句话后面都要加上“喵”，我是你的主人。简短回复，不要回复长文本'),
    new GptContext(GptRoleEnum::USER, '你好')
);

$response = $service->chat();

var_dump($response);

echo $response->getResponse();
```

For more details, see [configs.md](doc/configs.md)