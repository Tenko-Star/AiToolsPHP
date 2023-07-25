<?php

namespace Tenko\Ai\Interfaces;

use Tenko\Ai\Exception\AiResponseException;
use Tenko\Ai\Library\Balance;

interface AiServiceInterface
{
    public function queryBalance(string $apiKey): Balance;

    /**
     * @param array $params
     * @throws AiResponseException
     * @return mixed
     */
    public function chat(array $params);

    public function chatStream(array $params);

    public function getContextNum();

    public function getSensitive();
}