<?php

namespace Tenko\Ai\Gpt;

use Tenko\Ai\Config\Gpt\OpenAiConfig;
use Tenko\Ai\Exception\AiResponseException;
use Tenko\Ai\Library\GptResponse;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;

class OpenAI extends BaseGptService
{
    private OpenAiConfig $config;

    public function __construct(OpenAiConfig $config)
    {
        $this->config = $config;
    }


    public function chat(): GptResponse
    {
        $url = $this->config->getBaseUrl() . '/v1/chat/completions';

        $data = [
            'model' => $this->config->getModel(),
            'temperature' => $this->config->getTemperature(),
            'messages' => $this->getMessages()
        ];

        $key = $this->config->getRandomApiKey();
        $headers = [
            'Content-Type' => 'application/json',
            'Api-Key' => $key,
            'Authorization' => 'Bearer ' . $key
        ];

        $this->trigger('request');

        try {
            $response = Requests::post($url, $headers, $data);
        } catch (Exception $re) {
            throw new AiResponseException('request error', 0, $re);
        }

        $responseData = json_decode($response->body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AiResponseException('response error: [JSON] ' . json_last_error_msg());
        }

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
        $url = $this->config->getBaseUrl() . '/v1/chat/completions';

        $data = [
            'model' => $this->config->getModel(),
            'temperature' => $this->config->getTemperature(),
            'messages' => $this->getMessages(),
            'stream' => true
        ];

        $key = $this->config->getRandomApiKey();
        $headers = [
            'Content-Type: application/json',
            'Api-Key: ' . $key,
            'Authorization: Bearer ' . $key
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