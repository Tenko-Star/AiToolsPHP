<?php

namespace Tenko\Ai\Gpt;

use CurlHandle;
use JetBrains\PhpStorm\ArrayShape;
use Tenko\Ai\Library\GptContext;
use Tenko\Ai\Library\GptResponse;

abstract class BaseGptService
{
    #[ArrayShape([
        'onRequest' => 'callable',
        'onResponse' => 'callable',
        'onRead' => 'callable',
    ])]
    private array $events = [];

    /** @var array<GptContext> contexts */
    protected array $contexts = [];

    protected ?CurlHandle $curl = null;

    protected string $splitStr = 'data:';

    /**
     * @param array<GptContext> $contexts
     * @return BaseGptService
     */
    public function setContext(
        GptContext ...$contexts
    ): BaseGptService
    {
        $this->contexts = $contexts;

        return $this;
    }

    /**
     * Set event callback
     *
     * callbacks:<br>
     *  request(): void<br>
     *  response(array $responseData): void<br>
     *  read(string $content, array $raw): void
     *
     * @param string $event
     * @param callable $callback
     * @return $this
     */
    public function on(string $event, callable $callback): BaseGptService
    {
        $eventName = 'on' . ucfirst($event);

        $this->events[$eventName] = $callback;

        return $this;
    }

    protected function trigger(string $event, mixed ...$data): void
    {
        $eventName = 'on' . ucfirst($event);

        if (isset($this->events[$eventName]) && is_callable($this->events[$eventName])) {
            call_user_func_array($this->events[$eventName], $data);
        }
    }

    protected function getMessages(): array
    {
        $result = [];

        foreach ($this->contexts as $context) {
            $result[] = [
                'role' => $context->getRole(),
                'content' => $context->getContent()
            ];
        }

        return $result;
    }

    public abstract function chat(): GptResponse;

    public function stream(): GptResponse
    {
        $this->trigger('request');

        $this->streamInit();

        $responseData = [];
        $content = '';
        $buffer = '';
        $callback = function ($ch, $data) use (&$buffer, &$content, &$responseData) {
            $dataLen = strlen($data);
            if ($dataLen === 12 && $data === 'data: [DONE]') {
                $this->onStreamResponse(null);
                return 0;
            }

            $buffer .= $data;

            $jsonStrArray = explode("\n", $buffer);
            $jsonStrArrayCount = count($jsonStrArray);

            $prefixLen = strlen($this->splitStr);
            foreach ($jsonStrArray as $jsonStr) {
                $json = @json_decode(trim(substr($jsonStr, $prefixLen)), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $message = $this->onStreamResponse($json);
                    $content .= $message;

                    $responseData[] = $json;
                    $this->trigger('read', $message, $json);
                }
            }

            if (!str_ends_with($jsonStrArray[$jsonStrArrayCount - 1], '}')) {
                $buffer = $jsonStrArray[$jsonStrArrayCount - 1];
            }

            return $dataLen;
        };

        curl_setopt($this->curl, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($this->curl);

        $this->trigger('response', $responseData);

        return new GptResponse($content);
    }

    protected abstract function streamInit(): void;

    protected abstract function onStreamResponse(?array $data): string;
}