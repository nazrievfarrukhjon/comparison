<?php

namespace App\Modules\Blacklist;

class BlacklistESConfigs
{
    public function indexName(): string
    {
        return 'blacklist';
    }

    public function documentName(): string
    {
        return 'concat_names';
    }

    public function indexSettings(): array
    {
        return [
            'settings' => [
                'analysis' => [
                    'tokenizer' => [
                        'trigram_tokenizer' => [
                            'type' => 'ngram',
                            'min_gram' => 3,
                            'max_gram' => 3,
                        ],
                    ],
                    'analyzer' => [
                        'trigram_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'trigram_tokenizer',
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'properties' => [
                    'content' => [
                        'type' => 'text',
                        'analyzer' => 'trigram_analyzer',
                    ],
                ],
            ],
        ];
    }

}
