<?php

namespace App\Modules\Elasticsearch;

use App\Modules\Blacklist\BlacklistESConfigs;
use GuzzleHttp\Exception\GuzzleException;

/**
 *
 * In the context of deleting a document from Elasticsearch, the responsibility typically falls within the DOMAIN of the ElasticsearchDocument class.
 *
 * Here's why:
 *
 * Responsibility Alignment: The ElasticsearchDocument class represents a specific document within Elasticsearch. It encapsulates the document's data and provides operations related to that document, such as updating, retrieving, and deleting.
 *
 * Single Responsibility Principle (SRP): According to the SRP, each class should have a single responsibility. The responsibility of the ElasticsearchDocument class is to manage operations specific to a document, including deletion.
 *
 * High Cohesion: The ElasticsearchDocument class should have high cohesion, meaning that it should encapsulate related behaviors and data. Deleting a document is a core operation related to managing a document's lifecycle, and it makes sense for this functionality to be encapsulated within the ElasticsearchDocument class.
 */
class ElasticsearchDocument
{

    private string $indexName;
    private array $document;
    private string $documentId;
    private ElasticsearchGuzzle $elasticsearchGuzzle;
    private ElasticsearchIndex $elasticsearchIndex;
    private array $attributes;
    private string $esDocId;

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

    public static function newIndexNameAndEsDocIdAndEsGuzzleConstructor(
        string              $indexName,
        string              $esDocId,
        ElasticsearchGuzzle $elasticsearchGuzzle
    ): ElasticsearchDocument {
        $obj = new self();
        $obj->indexName = $indexName;
        $obj->elasticsearchGuzzle = $elasticsearchGuzzle;
        $obj->esDocId = $esDocId;

        return $obj;
    }

    public static function newEsGuzzleAndAttributesConstructor(ElasticsearchGuzzle $elasticsearchGuzzle, array $attributes): ElasticsearchDocument
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
        $document = [
            'id' => $this->attributes['id'],
            "name_combo" => $this->attributes['name_combo'],
        ];

        $this->elasticsearchGuzzle->add($this->attributes['index_name'], $document);
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
    public function deleteByEsId(): void
    {
        $this->elasticsearchGuzzle->deleteDocument($this->attributes['index_name'], $this->attributes['es_doc_id']);
    }

    /**
     * @throws GuzzleException
     */
    public function updateByEsId(): void
    {
        $document = [
            'name_combo' => $this->attributes['name_combo']
        ];
        $this->elasticsearchGuzzle->updateDocById(
            $this->attributes['index_name'],
            $this->attributes['es_doc_id'],
            $document
        );
    }

    /**
     * @throws GuzzleException
     */
    public function exactMatch(): array
    {
        return $this->elasticsearchGuzzle->exactMatch(
            $this->attributes['index_name'],
            $this->attributes['document_field'],
            $this->attributes['search_key']
        );
    }

}
