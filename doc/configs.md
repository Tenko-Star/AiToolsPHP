# Config

[中文说明](./configs.cn.md)

## GPT Config

### Common config

- gpt_type: `open_ai` or `azure`
- api_key: An array of API keys that, when used, will be selected in reverse order based on the incoming sequence (caching configuration required).
- model: The model to be used is specified in the `Tenko\Ai\Enum\OpenAiEnum` class, where you can find the available options for the model.
- temperature
- is_sensitive: Whether to perform sensitive word filtering.
- context_num: The maximum number of associated context.

### Openai config

- openai_agency_api: Proxy address

### Azure config

*Please refer to the official Microsoft documentation.*

- azure_resource_name
- azure_deployments
- azure_api_version

## Logger

If you want to output logs, please pass a class that implements the `Tenko\Ai\Interfaces\LogInterface` interface. If not provided, no logs will be recorded.

- usage

```php
\Tenko\Ai\AiConfig::setLogger(new class implements \Tenko\Ai\Interfaces\LogInterface {
    public static function info(string $message, array $data = []) {
        // implements
    }

    public static function warning(string $message, array $data = []) {
        // implements
    }

    public static function error(string $message, array $data = []) {
        // implements
    }

    public static function debug(string $message, array $data = []) {
        // implements
    }
});
```

## Cache

Some methods may utilize caching. While it is not mandatory to set up caching, not doing so might result in some functionalities being unavailable. The caching class needs to implement the `Tenko\Ai\Interfaces\CacheInterface` interface.

- usage

```php
\Tenko\Ai\AiConfig::setCache(new class implements \Tenko\Ai\Interfaces\CacheInterface {
    public function get(string $name, $default = null) {
        // some code...
        
        // return ...
    }

    public function set(string $name, $value): void {
        // some code
    }
});
```