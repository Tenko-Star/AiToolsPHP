<?php

namespace Tenko\Ai;

use JetBrains\PhpStorm\ArrayShape;
use Tenko\Ai\Enum\OpenAiEnum;
use Tenko\Ai\Exception\ConfigNotFoundException;
use Tenko\Ai\Interfaces\CacheInterface;
use Tenko\Ai\Interfaces\LogInterface;
use Tenko\Ai\Library\CacheStub;
use Tenko\Ai\Library\LogStub;

class AiConfig implements \ArrayAccess
{
    private array $config;

    private static ?LogInterface $logger = null;

    private static ?CacheInterface $cache = null;

    private static ?AiConfig $instance = null;

    public static function instance(array $config = []): AiConfig {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public static function init(
        #[ArrayShape([
        // common
        'gpt_type' => 'open_ai|azure',
        'api_key' => ['xxx', 'yyy'],
        'model' => OpenAiEnum::MODEL_GPT_35_TURBO,
        'max_tokens' => 150,
        'temperature' => 0.6,
        'is_sensitive' => 1,
        'context_num' => 2,

        // openai
        'agency_api' => 'https://xxx.xxx',

        // Azure
        'azure_resource_name' => '',
        'azure_deployments' => '',
        'azure_api_version' => ''
    ])] array $config
    ): void {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
    }

    private function __construct(array $config) {
        $this->config = $config;
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function isset(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * Get config or throw exception
     *
     * @param string $key
     * @return mixed
     * @throws ConfigNotFoundException
     */
    public function getOrFail(string $key) {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        throw new ConfigNotFoundException('Config key not exists: ' . $key);
    }

    /**
     * Check config or throw exception
     *
     * @param string $key
     * @return void
     * @throws ConfigNotFoundException
     */
    public function issetOrFail(string $key) {
        if (!isset($this->config[$key])) {
            throw new ConfigNotFoundException('Config key not exists: ' . $key);
        }
    }

    /**
     * Get all config
     *
     * @return array
     */
    public static function all(): array {
        return self::instance()->config;
    }

    public function offsetExists($offset): bool
    {
        return $this->isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {}

    public function offsetUnset($offset) {}

    /**
     * Set logger
     *
     * @param LogInterface $logger
     * @return void
     */
    public static function setLogger(LogInterface $logger) {
        self::$logger = $logger;
    }

    /**
     * Get logger
     *
     * @return LogInterface
     */
    public static function getLogger(): LogInterface {
        if (self::$logger === null) {
            return new LogStub();
        }

        return self::$logger;
    }

    /**
     * Set Cache class
     *
     * @param CacheInterface $cache
     * @return void
     */
    public static function setCache(CacheInterface $cache) {
        self::$cache = $cache;
    }

    /**
     * Set Cache class
     *
     * @return CacheInterface
     */
    public static function getCache(): CacheInterface {
        if (self::$cache === null) {
            return new CacheStub();
        }

        return self::$cache;
    }
}