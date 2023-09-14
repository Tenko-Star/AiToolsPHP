<?php

namespace Tenko\Test;

use PHPUnit\Framework\TestCase;
use Tenko\Ai\Config\Gpt\AzureConfig;
use Tenko\Ai\Config\Gpt\OpenAiConfig;
use Tenko\Ai\Enum\GptRoleEnum;
use Tenko\Ai\Gpt\Azure;
use Tenko\Ai\Gpt\OpenAI;
use Tenko\Ai\Library\GptContext;

class TestGpt extends TestCase
{
    private array $env;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->env = require './env.php';
    }

    private function getOpenAiConfig(): OpenAiConfig
    {
        $config = new OpenAiConfig();
        $config->setApiKeys($this->env['api_key_openai'] ?? []);
        $config->setAgencyUrl($this->env['openai_agency_api'] ?? '');

        return $config;
    }

    private function getAzureConfig(): AzureConfig
    {
        $config = new AzureConfig();
        $config->setApiVersion($this->env['azure_api_version'] ?? '');
        $config->setDeployments($this->env['azure_deployments'] ?? '');
        $config->setResourceName($this->env['azure_resource_name'] ?? '');
        $config->setApiKeys($this->env['api_key_azure'] ?? []);

        return $config;
    }

    public function testOpenAi()
    {
        $service = new OpenAI($this->getOpenAiConfig());
        $service->setContext(
            new GptContext(GptRoleEnum::SYSTEM, '现在你将模仿一只猫娘，与我对话每一句话后面都要加上“喵”，我是你的主人。简短回复，不要回复长文本'),
            new GptContext(GptRoleEnum::USER, '你好')
        );

        $result = $service->chat();

        $this->assertNotEmpty($result);

        $this->assertGreaterThan(0, $result->getResponseLength());

        echo "{$result->getResponse()}\n";
    }

    public function testAzure()
    {
        $service = new Azure($this->getAzureConfig());
        $service->setContext(
            new GptContext(GptRoleEnum::SYSTEM, '现在你将模仿一只猫娘，与我对话每一句话后面都要加上“喵”，我是你的主人。简短回复，不要回复长文本'),
            new GptContext(GptRoleEnum::USER, '你好')
        );

        $result = $service->chat();

        $this->assertNotEmpty($result);

        $this->assertGreaterThan(0, $result->getResponseLength());

        echo "{$result->getResponse()}\n";
    }

    public function testOpenAiStream()
    {
        $service = new OpenAI($this->getOpenAiConfig());
        $service->setContext(
            new GptContext(GptRoleEnum::SYSTEM, '现在你将模仿一只猫娘，与我对话每一句话后面都要加上“喵”，我是你的主人。简短回复，不要回复长文本'),
            new GptContext(GptRoleEnum::USER, '你好')
        );

        $total = '';
        $service->on('read', function ($message) use (&$total) {
            $total .= $message;
            echo $message;
        });

        $service->stream();

        $this->assertNotEmpty($total);

        echo $total;
    }

    public function testAzureStream()
    {
        $service = new Azure($this->getAzureConfig());
        $service->setContext(
            new GptContext(GptRoleEnum::SYSTEM, '现在你将模仿一只猫娘，与我对话每一句话后面都要加上“喵”，我是你的主人。简短回复，不要回复长文本'),
            new GptContext(GptRoleEnum::USER, '你好')
        );

        $total = '';
        $service->on('read', function ($message) use (&$total) {
            $total .= $message;
        });

        $service->stream();

        $this->assertNotEmpty($total);

        echo $total;
    }
}
