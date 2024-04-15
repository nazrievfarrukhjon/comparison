<?php

namespace App\Modules\Elasticsearch;

use App\Modules\Blacklist\BlacklistESConfigs;
use GuzzleHttp\Exception\GuzzleException;

class ElasticsearchIndex
{

    private string $indexName;

    private ElasticsearchDocument $elasticsearchDocument;
    private ElasticsearchGuzzle $elasticsearchGuzzle;
    private string $searchKey;

    public static function newIndexNameConstructor(string $indexName): ElasticsearchIndex
    {
        $obj = new self();
        $obj->indexName = $indexName;

        return $obj;
    }

    // use instead of constructor with no arg
    public static function newWithNoArg(): ElasticsearchIndex
    {
        return new self();
    }

    public static function newIndexNameAndEsDocumentConstructor(
        string $indexName,
        ElasticsearchDocument $elasticsearchDocument
    ): ElasticsearchIndex
    {
        $obj = new self();
        $obj->indexName = $indexName;
        $obj->elasticsearchDocument = $elasticsearchDocument;

        return $obj;
    }

    public static function newEsGuzzleAndSearchKeyConstructor(
        ElasticsearchGuzzle $elasticsearchGuzzle,
        string $searchKey
    ): ElasticsearchIndex
    {
        $obj = new self();
        $obj->elasticsearchGuzzle = $elasticsearchGuzzle;
        $obj->searchKey = $searchKey;
        return $obj;
    }

    /**
     * @throws GuzzleException
     */
    public function store(): void
    {
        $elasticsearchWithGuzzle = new ElasticsearchGuzzle();
        $blacklistESConfigs = new BlacklistESConfigs();
        $elasticsearchWithGuzzle->createIndex($this->indexName, $blacklistESConfigs->indexSettings());
    }

    /**
     * @throws GuzzleException
     */
    public function indices(): array
    {
        $elasticsearchWithGuzzle = new ElasticsearchGuzzle();
        return $elasticsearchWithGuzzle->indices();
    }

    public function index(): string
    {
        return $this->indexName;
    }
}
