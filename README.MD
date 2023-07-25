# Ai Tools PHP

This is a Util for ChatGPT and Azure.

## Quick Start

- ChatGPT:

```php
<?php

\Tenko\Ai\AiConfig::init([
    'gpt_type' => 'open_ai'
    // or 'gpt_type' => 'azure'
]);

$service = \Tenko\Ai\GptServiceFactory::create();

$service->chatStream([]);

```

- TTS

```php

```

For more details see [configs.md](./doc/configs.md)