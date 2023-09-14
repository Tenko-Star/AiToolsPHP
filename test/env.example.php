<?php

return [
    // require
    'api_key_openai' => [
        '123456',
        '234567',
        '345678'
    ],
    'api_key_azure' => [
        '123456',
        '234567',
        '345678'
    ],

    // OpenAI(optional)
    'openai_agency_api' => '',

    // Azure (require)
    'azure_resource_name' => 'test',
    'azure_deployments' => 'test-gpt35',
    'azure_api_version' => '2023-03-15-preview',

    // optional
    'model' => '', // default gpt-3.5-turbo
    'max_length' => 150,
    'temperature' => 0.6,
    'context_num' => 2,
];