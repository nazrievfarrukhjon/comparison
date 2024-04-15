<?php

namespace App\Modules\Elasticsearch\Controllers;

use App\Modules\Elasticsearch\ElasticsearchDocument;
use App\Modules\Elasticsearch\ElasticsearchGuzzle;
use App\Modules\Elasticsearch\ElasticsearchIndex;
use App\Modules\Elasticsearch\Requests\AddDocumentRequest;
use App\Modules\Elasticsearch\Requests\CreateIndexRequest;
use App\Modules\Elasticsearch\Requests\DocumentRequest;
use App\Modules\Elasticsearch\Requests\ElasticSearchRequest;
use App\Modules\Elasticsearch\Requests\ExactSearchRequest;
use App\Modules\Elasticsearch\Requests\UpdateDocumentRequest;
use App\Modules\InputAttributes;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElasticsearchController
{

    /**
     * @throws GuzzleException
     */
    public function fuzzyMatch(ElasticSearchRequest $request): JsonResponse
    {
        $elasticsearchDocument = ElasticsearchDocument::newEsGuzzleAndAttributesConstructor(
            new ElasticsearchGuzzle(),
            InputAttributes::newArrayConstructor($request->all())
        );

        $fuzzyMatch = $elasticsearchDocument->fuzzyMatch();

        return response()->json($fuzzyMatch);
    }

    /**
     * @throws GuzzleException
     */
    public function addDocument(AddDocumentRequest $request): JsonResponse
    {
        $elasticsearchDocument = ElasticsearchDocument::newEsGuzzleAndAttributesConstructor(
            new ElasticsearchGuzzle(),
            InputAttributes::newArrayConstructor($request->all())
        );
        $elasticsearchDocument->add();

        return response()->json(['document stored!']);
    }


    /**
     * @throws GuzzleException
     *
     * In the context of deleting a document from Elasticsearch, the responsibility typically falls within the domain of the ElasticsearchDocument class.
     *
     * Here's why:
     *
     * Responsibility Alignment: The ElasticsearchDocument class represents a specific document within Elasticsearch. It encapsulates the document's data and provides operations related to that document, such as updating, retrieving, and deleting.
     *
     * Single Responsibility Principle (SRP): According to the SRP, each class should have a single responsibility. The responsibility of the ElasticsearchDocument class is to manage operations specific to a document, including deletion.
     *
     * High Cohesion: The ElasticsearchDocument class should have high cohesion, meaning that it should encapsulate related behaviors and data. Deleting a document is a core operation related to managing a document's lifecycle, and it makes sense for this functionality to be encapsulated within the ElasticsearchDocument class.
     */
    public function deleteDocument(DocumentRequest $request): JsonResponse
    {
        $elasticsearchDocument = ElasticsearchDocument::newEsGuzzleAndAttributesConstructor(
            new ElasticsearchGuzzle(),
            InputAttributes::newArrayConstructor($request->all())
        );

        $elasticsearchDocument->deleteByEsId();

        return response()->json(['document deleted!']);
    }

    /**
     * @throws GuzzleException
     */
    public function updateDocument(UpdateDocumentRequest $request): JsonResponse
    {
        $elasticsearchDocument = ElasticsearchDocument::newEsGuzzleAndAttributesConstructor(
            new ElasticsearchGuzzle(),
            InputAttributes::newArrayConstructor($request->all())
        );

        $elasticsearchDocument->updateByEsId();

        return response()->json(['document updated!']);
    }

    /**
     * $settings = [
     * 'settings' => [
     * 'analysis' => [
     * 'tokenizer' => [
     * 'trigram_tokenizer' => [
     * 'type' => 'ngram',
     * 'min_gram' => 3,
     * 'max_gram' => 3,
     * ],
     * ],
     * 'analyzer' => [
     * 'trigram_analyzer' => [
     * 'type' => 'custom',
     * 'tokenizer' => 'trigram_tokenizer',
     * ],
     * ],
     * ],
     * ],
     * 'mappings' => [
     * 'properties' => [
     * 'content' => [
     * 'type' => 'text',
     * 'analyzer' => 'trigram_analyzer',
     * ],
     * ],
     * ],
     * ];
     * @throws GuzzleException
     */
    public function createIndex(CreateIndexRequest $request): JsonResponse
    {
        $indicesOfElasticsearch = ElasticsearchIndex::newIndexNameConstructor($request->input('index_name'));
        $indicesOfElasticsearch->store();

        return response()->json(['index created']);
    }

    /**
     * @throws GuzzleException
     */
    public function indexContent(Request $request): JsonResponse
    {
        $elasticsearchDocument = ElasticsearchDocument::newIndexConstructor($request->input('index_name'));
        $response = $elasticsearchDocument->indexContent();

        return response()->json(['response' => $response]);
    }

    /**
     * @throws GuzzleException
     */
    public function indices(): JsonResponse
    {
        $indicesOfElasticsearch = ElasticsearchIndex::newWithNoArg();
        $indices = $indicesOfElasticsearch->indices();

        return response()->json(['indices' => $indices]);
    }

    /**
     * @throws GuzzleException
     */
    public function exactMatch(ExactSearchRequest $request): JsonResponse
    {
        $elasticsearchDocument = ElasticsearchDocument::newEsGuzzleAndAttributesConstructor(
            new ElasticsearchGuzzle(),
            InputAttributes::newArrayConstructor($request->all())
        );

        $exactMatch = $elasticsearchDocument->exactMatch();

        return response()->json($exactMatch);
    }

    /**
     */
    public function search(ElasticSearchRequest $request): JsonResponse
    {
        return response()->json([]);
    }


    /////////////
    public function wildCardSearch(ElasticSearchRequest $request): JsonResponse
    {
        return response()->json([]);
    }

    /**
     */
    public function addDocumentWithId(AddDocumentRequest $request, int $id): JsonResponse
    {
        return response()->json([]);
    }

    /**
     * @throws GuzzleException
     */
    public function qQuery(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    public function deleteIndexTotally(DeleteIndexRequest $request): JsonResponse
    {
        return response()->json([]);
    }

    public function deleteIndex(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    public function updateIndex(Request $request)
    {
    }

    public function countIndexDocuments(Request $request): JsonResponse
    {
        return response()->json([]);
    }

}
