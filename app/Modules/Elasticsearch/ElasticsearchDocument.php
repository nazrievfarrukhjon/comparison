<?php

namespace App\Modules\Elasticsearch;

use App\Modules\Blacklist\BlacklistESConfigs;
use GuzzleHttp\Exception\GuzzleException;

class ElasticsearchDocument
{

    private string $indexName;
    private array $document;

    public static function newIndexConstructor(string $indexName): ElasticsearchDocument
    {
        $obj = new self();
        $obj->indexName = $indexName;

        return $obj;
    }

    public static function newIndexAndDocumentConstructor(string $indexName, array $document): ElasticsearchDocument
    {
        $obj = new self();
        $obj->indexName = $indexName;
        $obj->document = $document;

        return $obj;
    }

    public static function newDefaultConstructor(): ElasticsearchDocument
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

    /**
     * @throws GuzzleException
     */
    public function add(): void
    {
        $elasticsearchWithGuzzle = new ElasticsearchWithGuzzle();
        $elasticsearchWithGuzzle->add($this->indexName, $this->document);
    }

    /**
     * @throws GuzzleException
     */
    public function indexContent(): array
    {
        $elasticsearchWithGuzzle = new ElasticsearchWithGuzzle();

        return $elasticsearchWithGuzzle->indexContent($this->indexName);
    }

}
