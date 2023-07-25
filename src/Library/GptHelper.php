<?php

namespace Tenko\Ai\Library;

class GptHelper
{
    public static function requestByStream(callable $callback, array $curlOptions = []) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("X-Accel-Buffering: no");

        $ch = curl_init();
        foreach ($curlOptions as $curlKey => $curlOption) {
            curl_setopt($ch, $curlKey, $curlOption);
        }
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
        curl_exec($ch);
    }

    public static function parseData($stream): array {
        $dataLists = explode("\n\n", $stream);
        $content = '';
        foreach ($dataLists as $data) {
            if (str_contains($data, 'data: [DONE]')) {
                continue;
            }
            if (str_contains($data, 'data:')) {
                $data = str_replace("data: ", "", $data);
                $data = json_decode($data, true);
                $content .= $data['choices'][0]['delta']['content'] ?? '';
                if (empty($content)) {
                    continue;
                }
            }
        }
        return ['content' => $content];
    }
}