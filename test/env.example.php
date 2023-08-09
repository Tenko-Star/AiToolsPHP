<?php

return [
    // require
    'api_key' => [
        '123456',
        '234567',
        '345678'
    ],
    'gpt_type' => 'azure',

    // OpenAI(optional)
    'agency_api' => '',

    // Azure (require)
    'azure_resource_name' => 'test',
    'azure_deployments' => 'test-gpt35',
    'azure_api_version' => '2023-03-15-preview',

    // optional
    'model' => '', // default gpt-3.5-turbo
    'max_tokens' => 150,
    'temperature' => 0.6,
    'is_sensitive' => 1,
    'context_num' => 2,
    'cache_class' => 'path\\to\\Cache',
    'logger_class' => 'path\\to\\Log',
];