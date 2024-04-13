<?php

namespace App\Modules\Elasticsearch;

use App\Modules\Blacklist\BlacklistESConfigs;
use GuzzleHttp\Exception\GuzzleException;

class IndicesOfElasticsearch
{

    private string $indexName;

    // use instead of constructor with one string arg
    public static function newWithIndex(string $indexName): IndicesOfElasticsearch
    {
        $obj = new self();
        $obj->indexName = $indexName;

        return $obj;
    }

    // use instead of constructor with no arg
    public static function newWithNoArg(): IndicesOfElasticsearch
    {
        return new self();
    }

    /**
     * @throws GuzzleException
     */
    public function store(): void
    {
        $elasticsearchWithGuzzle = new ElasticsearchWithGuzzle();
        $blacklistESConfigs = new BlacklistESConfigs();
        $elasticsearchWithGuzzle->createIndex($this->indexName, $blacklistESConfigs->indexSettings());
    }

    /**
     * @throws GuzzleException
     */
    public function indices(): array
    {
        $elasticsearchWithGuzzle = new ElasticsearchWithGuzzle();
        return $elasticsearchWithGuzzle->indices();
    }

}
