<?php

namespace Tenko\Ai\Gpt;

use Tenko\Ai\Config\Gpt\AzureConfig;
use Tenko\Ai\Exception\AiResponseException;
use Tenko\Ai\Library\GptResponse;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;

class Azure extends BaseGptService
{
    private AzureConfig $config;

    public function __construct(AzureConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return GptResponse
     * @throws AiResponseException
     */
    public function chat(): GptResponse
    {
        $url = $this->config->getApiUrl('/deployments/{Deployments}/chat/completions?api-version={ApiVersion}');

        $data = [
            'model' => $this->config->getModel(),
            'temperature' => $this->config->getTemperature(),
            'messages' => $this->getMessages()
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Api-Key' => $this->config->getRandomApiKey()
        ];

        $this->trigger('request');

        try {
            $response = Requests::post($url, $headers, json_encode($data));
        } catch (Exception $re) {
            throw new AiResponseException('request error', 0, $re);
        }

        $responseData = json_decode($response->body, true);

        $this->trigger('response', $responseData);

        if (isset($responseData['error'])) {
            throw new AiResponseException($responseData['error']['message']);
        }

        if (!isset($responseData['choices'][0]['message'])) {
            throw new AiResponseException('no message');
        }

        $message = $responseData['choices'][0]['message'];

        return new GptResponse($message['content']);
    }

    protected function streamInit(): void
    {
        $url = $this->config->getApiUrl('/deployments/{Deployments}/chat/completions?api-version={ApiVersion}');

        $headers = [
            'Content-Type: application/json',
            'Api-Key: ' . $this->config->getRandomApiKey()
        ];

        $data = [
            'model' => $this->config->getModel(),
            'temperature' => $this->config->getTemperature(),
            'messages' => $this->getMessages(),
            'stream' => true
        ];

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_BUFFERSIZE, 1024);
    }

    protected function onStreamResponse(?array $data): string
    {
        if ($data === null) {
            return '';
        }

        if (isset($data['choices'][0]['delta']['content']) && $data['choices'][0]['finish_reason'] === NULL) {
            return $data['choices'][0]['delta']['content'];
        }

        return '';
    }
}