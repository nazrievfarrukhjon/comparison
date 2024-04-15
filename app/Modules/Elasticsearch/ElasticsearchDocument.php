<?php

namespace App\Modules\Elasticsearch;

use App\Modules\Blacklist\BlacklistESConfigs;
use GuzzleHttp\Exception\GuzzleException;

class ElasticsearchDocument
{

    private string $indexName;
    private array $document;
    private string $documentId;
    private ElasticsearchGuzzle $elasticsearchGuzzle;
    private ElasticsearchIndex $elasticsearchIndex;
    private array $attributes;

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

    public static function newElasticsearchGuzzleAndDocIdConstructor(ElasticsearchGuzzle $elasticsearchGuzzle, string $documentId): ElasticsearchDocument
    {
        $obj = new self();
        $obj->elasticsearchGuzzle = $elasticsearchGuzzle;
        $obj->documentId = $documentId;

        return $obj;
    }


    public static function newIndexAndDocumentIdConstructor(ElasticsearchIndex $elasticsearchIndex, string $documentId): ElasticsearchDocument
    {
        $obj = new self();
        $obj->elasticsearchIndex = $elasticsearchIndex;
        $obj->documentId = $documentId;

        return $obj;
    }

    public static function newDefaultConstructor(): ElasticsearchDocument
    {
        return new self();
    }

    public static function newDocumentIdConstructor(string $documentId): ElasticsearchDocument
    {
        $obj = new self();
        $obj->documentId = $documentId;
        return $obj;
    }

    public static function newESGuzzleDocumentIdConstructor(ElasticsearchGuzzle $elasticsearchGuzzle, string $documentId): ElasticsearchDocument
    {
        $obj = new self();
        $obj->elasticsearchGuzzle = $elasticsearchGuzzle;
        $obj->documentId = $documentId;

        return $obj;
    }

    public static function newESGuzzleAndDocAttributesConstructor(ElasticsearchGuzzle $elasticsearchGuzzle, array $attributes): ElasticsearchDocument
    {
        $obj = new self();
        $obj->elasticsearchGuzzle = $elasticsearchGuzzle;
        $obj->attributes = $attributes;

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

    /**
     * @throws GuzzleException
     */
    public function add(): void
    {
        $elasticsearchWithGuzzle = new ElasticsearchGuzzle();
        $elasticsearchWithGuzzle->add($this->indexName, $this->document);
    }

    /**
     * @throws GuzzleException
     */
    public function indexContent(): array
    {
        $elasticsearchWithGuzzle = new ElasticsearchGuzzle();

        return $elasticsearchWithGuzzle->indexContent($this->indexName);
    }

    /**
     * @throws GuzzleException
     */
    public function delete(string $indexName): void
    {
        $this->elasticsearchGuzzle->deleteDocument($indexName, $this->documentId);
    }

    /**
     * @throws GuzzleException
     */
    public function update(string $indexName): void
    {
        $document = [
            'name_combo' => $this->attributes['name_combo']
        ];
        $this->elasticsearchGuzzle->updateDocById($indexName, $this->attributes['document_id'], $document);
    }

}
