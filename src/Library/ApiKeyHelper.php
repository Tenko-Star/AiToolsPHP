<?php

namespace Tenko\Ai\Library;

use Tenko\Ai\Exception\AiException;
use Tenko\Ai\Interfaces\CacheInterface;

class ApiKeyHelper
{
    const CACHE_NAME = 'ai_api_keys';

    /** @var array<string> $keys */
    private array $keys;

    private ?CacheInterface $cache = null;

    /**
     * @param array $keys
     * @param CacheInterface $cache
     * @throws AiException
     */
    public function __construct(array $keys, CacheInterface $cache) {
        $this->cache = $cache;
        $cacheKey = $cache->get(self::CACHE_NAME, []);

        if (is_array($cacheKey) && count($cacheKey) > 0) {
            $this->keys = $cacheKey;
            return;
        }

        if (count($keys) === 0) {
            throw new AiException('Api keys is empty');
        }

        $this->keys = $keys;
    }

    public function getKey(): string {
        $key = array_pop($this->keys);
        $this->cache->set(self::CACHE_NAME, $this->keys);

        return $key;
    }
}