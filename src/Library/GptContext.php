<?php

namespace Tenko\Ai\Library;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use Tenko\Ai\Enum\GptRoleEnum;

final class GptContext
{
    private string $role;

    private string $content;

    /**
     * @param string $role
     * @param string $content
     */
    public function __construct(
        #[ExpectedValues(valuesFromClass: GptRoleEnum::class)]
        string $role,
        string $content
    )
    {
        $this->role = $role;
        $this->content = $content;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array<GptContext>
     */
    public static function construct(
        #[ArrayShape([
            [
                'role' => 'string',
                'content' => 'string'
            ]
        ])]
        array $data
    ): array
    {
        $result = [];

        foreach ($data as $datum) {
            $result[] = new GptContext($datum['role'], $datum['content']);
        }

        return $result;
    }
}