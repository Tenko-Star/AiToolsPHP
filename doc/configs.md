# Config

## GPT Config

### Common config

```php
abstract class BaseGptConfig {
    public function setApiKeys(array $apiKeys): BaseGptConfig;
    
    // Default: OpenAiEnum::MODEL_GPT_35_TURBO
    public function setModel(
        #[ExpectedValues(valuesFromClass: OpenAiEnum::class)]
        string $model
    ): BaseGptConfig;
    
    // Default: 0.6
    public function setTemperature(float $temperature): BaseGptConfig;
}
```

- apiKeys: An array of API keys that, when used, will be selected in reverse order based on the incoming sequence.
- model: The model to be used is specified in the `Tenko\Ai\Enum\OpenAiEnum` class, where you can find the available options for the model.
- temperature

### Openai config

```php
class OpenAiConfig extends BaseGptConfig {
    public function setAgencyUrl(string $agencyUrl): OpenAiConfig;
}
```

- agencyUrl: Proxy address

### Azure config

```php
class AzureConfig extends BaseGptConfig {
    public function setResourceName(string $resourceName): AzureConfig;
    public function setDeployments(string $deployments): AzureConfig;
    public function setApiVersion(string $apiVersion): AzureConfig;
}
```

*Please refer to the official Microsoft documentation.*

- azure_resource_name
- azure_deployments
- azure_api_version