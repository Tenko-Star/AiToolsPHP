<?php

namespace Tenko\Ai\AiServices;

use Tenko\Ai\Enum\OpenAiEnum;
use Tenko\Ai\Exception\AiResponseException;
use Tenko\Ai\Exception\ConfigNotFoundException;
use Tenko\Ai\Interfaces\AiServiceInterface;
use Tenko\Ai\AiConfig;
use Tenko\Ai\Library\ApiKeyHelper;
use Tenko\Ai\Library\Balance;
use Tenko\Ai\Library\GptHelper;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;

class AzureService implements AiServiceInterface
{
    private string $baseUri = 'https://{ResourceName}.openai.azure.com/openai';

    private string $resourceName = '';

    private string $deployments = '';

    private string $apiVersion = '';

    private string $apiKey = '';

    private string $model = '';

    protected float $temperature = 0.6;

    private int $maxSession = 5;

    protected int $isSensitive = 1;      //敏感词过滤

    /**
     * @throws ConfigNotFoundException
     */
    public function __construct()
    {
        $config = AiConfig::instance();
        $keyHelper = new ApiKeyHelper($config->getOrFail('api_key'), AiConfig::getCache());

        $this->resourceName = $config->getOrFail('azure_resource_name');
        $this->deployments = $config->getOrFail('azure_deployments');
        $this->apiVersion = $config->getOrFail('azure_api_version');
        $this->apiKey = $keyHelper->getKey();
        $this->model = $config->get('model', OpenAiEnum::MODEL_GPT_35_TURBO);
        $this->maxSession = $config->get('context_num', 5);
        $this->isSensitive = $config->get('is_sensitive', 1);
    }

    public function queryBalance($apiKey): Balance
    {
        return new Balance();
    }

    /**
     * @inheritDoc
     */
    public function chat(array $params, ?int $groupId = null): array
    {
        $api = '/deployments/{Deployments}/chat/completions?api-version={ApiVersion}';

        $url = $this->parseUrl($this->baseUri . $api);

        $data = [
            'model' => $this->model, //聊天模型
            'messages' => $params['messages'],
            'temperature' => $this->temperature,
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'api-key' => $this->apiKey
        ];

        try {
            $response = Requests::post($url, $headers, json_encode($data), ['timeout' => 100]);
        } catch (Exception $re) {
            throw new AiResponseException('request error', 0, $re);
        }

        $responseData = json_decode($response->body, true);
        if (isset($responseData['error'])) {
            throw new AiResponseException($responseData['error']['message']);
        }

        return $responseData;
    }

    public function chatStream(array $params, ?int $groupId = null, string $splitStr = 'data: '): array
    {
        $api = '/deployments/{Deployments}/chat/completions?api-version={ApiVersion}';
        $url = $this->parseUrl($this->baseUri . $api);

        $content = '';
        $response = true;
        $callback = function ($ch, $data) use (&$content, &$response, &$total, $splitStr) {
            $logger = AiConfig::getLogger();
            $logger->debug('stream raw data', ['raw_data' => $data]);
            $result = @json_decode($data);

            if (isset($result->error)) {
                $response = $result->error->message;
            } else {
                $parseData = GptHelper::parseData($data);
                $logger->debug('stream data', [
                    'json_data' => json_encode($parseData)
                ]);
                $content .= $parseData['content'];

                if (str_starts_with($data, 'data: ')) {
                    $data = substr($data, 6);
                }

                echo $splitStr . $data;
                ob_flush();
                flush();
            }
            return strlen($data);
        };

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'api-key: ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 100,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $this->model, //聊天模型
                'messages' => $params['messages'],
                'stream' => true,
                'temperature' => $this->temperature,
            ])
        ];

        $preHandler = null;
        if ($groupId) {
            $preHandler = function () use ($groupId) {
                echo 'data: ' . json_encode(['group_id' => $groupId]);
                ob_flush();
                flush();
            };
        }

        GptHelper::requestByStream($callback, $options, $preHandler);


        if (true !== $response) {
            throw new AiResponseException($response);
        }
        return ['content' => $content];
    }

    public function getContextNum()
    {
        return $this->maxSession;
    }

    public function getSensitive()
    {
        return $this->isSensitive;
    }

    private function parseUrl(string $url)
    {
        return str_replace(
            ['{ResourceName}', '{Deployments}', '{ApiVersion}'],
            [$this->resourceName, $this->deployments, $this->apiVersion],
            $url
        );
    }
}