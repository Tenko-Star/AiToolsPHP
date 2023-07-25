<?php

namespace Tenko\Ai\Enum;

class OpenAiEnum
{

    const MODEL_TEXT_DAVINCI_003        = "text-davinci-003";
    const MODEL_GPT_35_TURBO            = "gpt-3.5-turbo";
    const MODEL_GPT_35_TURBO_0301       = "gpt-3.5-turbo-0301";
    const MODEL_CODE_DAVINCI_002        = "code-davinci-002";
    const MODEL_GPT_4                   = "gpt-4";
    const MODEL_GPT_4_0314              = "gpt-4-0314";
    const MODEL_GPT_4_32k               = "gpt-4-32k";
    const MODEL_GPT_4_32k_0314          = "gpt-4-32k-0314";


    public static function getModelLists(string $type)
    {
        switch ($type) {
            case 'open_ai':
                return [
                    self::MODEL_TEXT_DAVINCI_003,
                    self::MODEL_GPT_35_TURBO,
                    self::MODEL_GPT_35_TURBO_0301,
                    self::MODEL_CODE_DAVINCI_002,
                    self::MODEL_GPT_4,
                    self::MODEL_GPT_4_0314,
                    self::MODEL_GPT_4_32k,
                    self::MODEL_GPT_4_32k_0314,
                ];
            case 'azure':
                return [
                    self::MODEL_TEXT_DAVINCI_003,
                    self::MODEL_GPT_35_TURBO,
                    self::MODEL_GPT_35_TURBO_0301,
                    self::MODEL_CODE_DAVINCI_002,
                ];
        }

        return [];
    }



}