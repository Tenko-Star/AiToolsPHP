<?php

namespace Tenko\Ai\AiServices;

use Tenko\Ai\AiConfig;
use Tenko\Ai\Enum\OpenAiEnum;
use Tenko\Ai\Exception\AiException;
use Tenko\Ai\Exception\AiResponseException;
use Tenko\Ai\Interfaces\AiServiceInterface;
use Tenko\Ai\Library\ApiKeyHelper;
use Tenko\Ai\Library\Balance;
use Tenko\Ai\Library\GptHelper;
use WpOrg\Requests\Exception;
use WpOrg\Requests\Requests;

class OpenAiService implements AiServiceInterface
{

    protected $apiKey = '';
    protected $baseUrl = 'https://api.openai.com';
    protected AiConfig $config;
    protected $model = '';
    protected $maxTokens = 150;
    protected $temperature = 0.6;
    protected $contextNum = 3;      //联系上下文
    protected $isSensitive = 1;      //敏感词过滤
    protected $headers = [];


    /**
     * @throws AiException
     */
    public function __construct()
    {
        $this->config = AiConfig::instance();

        //ai的key设置缓存
        $keyHelper = new ApiKeyHelper($this->config['api_key'], AiConfig::getCache());
        $this->apiKey = $keyHelper->getKey();

        //模型
        $this->model = $this->config->get('model', OpenAiEnum::MODEL_GPT_35_TURBO);
        //最大长度
        $this->maxTokens = $this->config->get('max_tokens', 150);
        //温度
        $this->temperature = $this->config->get('temperature', 0.6);
        //敏感词过滤
        $this->isSensitive = $this->config->get('is_sensitive',1); //默认开启
        //联系上下文
        $this->contextNum = $this->config->get('context_num', 2);
        //代理域名
        if(!empty($this->config['agency_api'])){
            $this->baseUrl = $this->config['agency_api'];
        }
        $this->headers['Content-Type'] = 'application/json';
        $this->headers['Authorization'] = 'Bearer '.$this->apiKey;
    }

    public function queryBalance($apiKey): Balance
    {
        $headers = $this->headers;
        $headers['Authorization'] = 'Bearer '.$apiKey;

        try {//先调用接口获取总量和订阅的有效期
            $url = $this->baseUrl . '/v1/dashboard/billing/subscription';
            $response = Requests::get($url, $headers);
            $responseData = $this->getResponseData($response);
            $hardLimitUsd = $responseData['hard_limit_usd'];            //总量
            $accessUntil = $responseData['access_until'];               //订阅的有效期

            //查询最近90天使用的余额
            $endDate = date('Y-m-d', strtotime("+1 day"));
            $startDate = date('Y-m-d', strtotime("-90 day"));
            $url = $this->baseUrl . "/v1/dashboard/billing/usage?start_date=$startDate&&end_date=$endDate";
            $response = Requests::get($url, $headers);
            $responseData = $this->getResponseData($response);
            $totalUsage = round($responseData['total_usage'] / 100, 2);  //已使用的余额
            $surplus = round($hardLimitUsd - $totalUsage, 2);
        } catch (Exception $re) {
            // Throw from Request::get or post
            throw new AiException('Request error', 0, $re);
        }

        return new Balance(
            $hardLimitUsd,
            $totalUsage,
            $surplus,
            $accessUntil
        );
    }

    public function chat(array $params): array
    {

        $this->baseUrl.='/v1/chat/completions';
        $data = [
            'model' => $this->model, //聊天模型
            'messages' => $params['messages'],
            'temperature'   => $this->temperature,
        ];
        //设置超时时间
        $options['timeout'] = 100;
        $response = Requests::post($this->baseUrl, $this->headers,json_encode($data),$options);
        return $this->getResponseData($response);

    }

    public function chatStream(array $params): array
    {

        $this->baseUrl.='/v1/chat/completions';

        $content = '';
        $response = true;
        $callback = function ($ch, $data) use (&$content,&$response,&$total){
            $logger = AiConfig::getLogger();
            $logger->debug('stream raw data', $data);
            $result = @json_decode($data);

            if (isset($result->error)) {
                $response = $result->error->message;
            }else{
                $parseData = GptHelper::parseData($data);
                $logger->debug('stream data', [
                    'json_data' => json_encode($parseData)
                ]);
                $content.= $parseData['content'];
                echo $data;
                ob_flush();
                flush();
            }
            return strlen($data);
        };

        GptHelper::requestByStream($callback, [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer '.$this->apiKey
            ],
            CURLOPT_TIMEOUT => 100,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode([
                'model'         => $this->model, //聊天模型
                'messages'      => $params['messages'],
                'stream'        => true ,
                'temperature'   => $this->temperature,
            ])
        ]);


        if(true !== $response){
            throw new AiResponseException($response);
        }
        return ['content'=>$content];

    }

    public function getResponseData($response):array
    {
        $responseData = json_decode($response->body,true);
        if(isset($responseData['error'])){
            throw new AiResponseException($responseData['error']['message']);
        }
        return $responseData;

    }


    public function getContextNum()
    {
        return $this->contextNum;

    }

    public function getSensitive()
    {
        return $this->isSensitive;
    }
}