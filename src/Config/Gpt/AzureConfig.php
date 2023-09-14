<?php

namespace Tenko\Ai\Config\Gpt;

use Tenko\Ai\Config\BaseGptConfig;

final class AzureConfig extends BaseGptConfig
{
    private const BASE_URL = 'https://{ResourceName}.openai.azure.com/openai';

    private string $resourceName = '';

    private string $deployments = '';

    private string $apiVersion = '';

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function setResourceName(string $resourceName): AzureConfig
    {
        $this->resourceName = $resourceName;
        return $this;
    }

    public function getDeployments(): string
    {
        return $this->deployments;
    }

    public function setDeployments(string $deployments): AzureConfig
    {
        $this->deployments = $deployments;
        return $this;
    }

    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    public function setApiVersion(string $apiVersion): AzureConfig
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    public function getApiUrl(string $api): string
    {
        return str_replace(
            ['{ResourceName}', '{Deployments}', '{ApiVersion}'],
            [$this->resourceName, $this->deployments, $this->apiVersion],
            (self::BASE_URL . $api)
        );
    }
}