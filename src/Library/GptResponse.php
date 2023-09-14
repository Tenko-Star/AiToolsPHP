<?php

namespace Tenko\Ai\Library;

final class GptResponse
{
    private string $response;

    private int $responseLength;

    /**
     * @param string $response
     * @param int $responseLength
     */
    public function __construct(string $response)
    {
        $this->response = $response;
        $this->responseLength = strlen($response);
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getResponseLength(): int
    {
        return $this->responseLength;
    }

}