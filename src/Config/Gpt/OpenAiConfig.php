<?php

namespace Tenko\Ai\Config\Gpt;

use Tenko\Ai\Config\BaseGptConfig;

final class OpenAiConfig extends BaseGptConfig
{
    private const BASE_URL = 'https://api.openai.com';

    private ?string $agencyUrl = null;

    public function getAgencyUrl(): ?string
    {
        return $this->agencyUrl;
    }

    public function setAgencyUrl(string $agencyUrl): OpenAiConfig
    {
        $this->agencyUrl = $agencyUrl ?: null;
        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->agencyUrl ?: self::BASE_URL;
    }
}