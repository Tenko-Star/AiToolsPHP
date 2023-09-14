<?php

namespace Tenko\Ai\Config;

use JetBrains\PhpStorm\ExpectedValues;
use Tenko\Ai\Enum\OpenAiEnum;

abstract class BaseGptConfig
{
    private array $apiKeys = [];

    #[ExpectedValues(valuesFromClass: OpenAiEnum::class)]
    private string $model = OpenAiEnum::MODEL_GPT_35_TURBO;

    private int $maxLength = 150;

    private float $temperature = 0.6;

    public function getApiKeys(): array
    {
        return $this->apiKeys;
    }

    public function setApiKeys(array $apiKeys): BaseGptConfig
    {
        $this->apiKeys = array_filter(
            $apiKeys,
            fn($key) => is_string($key) && !empty($key) && !str_starts_with($key, '#')
        );

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(
        #[ExpectedValues(valuesFromClass: OpenAiEnum::class)]
        string $model
    ): BaseGptConfig
    {
        $this->model = $model;
        return $this;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function setMaxLength(int $maxLength): BaseGptConfig
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): BaseGptConfig
    {
        $this->temperature = $temperature;
        return $this;
    }

    public function getRandomApiKey(): string
    {
        $count = count($this->apiKeys);
        if ($count === 1) {
            return $this->apiKeys[0];
        }

        $random = rand(0, $count - 1);
        return $this->apiKeys[$random];
    }
}