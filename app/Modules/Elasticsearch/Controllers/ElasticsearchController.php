<?php

namespace App\Modules\Elasticsearch\Controllers;

use App\Modules\Elasticsearch\ElasticsearchDocument;
use App\Modules\Elasticsearch\ElasticsearchWithGuzzle;
use App\Modules\Elasticsearch\IndicesOfElasticsearch;
use App\Modules\Elasticsearch\Requests\AddDocumentRequest;
use App\Modules\Elasticsearch\Requests\CreateIndexRequest;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ElasticsearchController
{
    /**
     */
    public function search(ElasticSearchRequest $request): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->search(
                $request->input('document_name'),
                $request->input('search_key'),
            );

        return response()->json($response, $status);
    }

    public function wildCardSearch(ElasticSearchRequest $request): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->wildCardSearch(
                $request->input('document_name'),
                $request->input('search_key'),
            );

        return response()->json($response, $status);
    }

    /**
     * @throws GuzzleException
     */
    public function fuzzySearch(ElasticSearchRequest $request): JsonResponse
    {
        $latinInits = StrConverter::convertCyrillicToLatin($request->input('search_key'));
        $latinInits = Str::lower(preg_replace('/[^a-zA-Z]/', '', $latinInits));

        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->fuzzySearch(
                $request->input('document_name'),
                $latinInits,
            );

        return response()->json($response, $status);
    }

    /**
     * @throws GuzzleException
     */
    public function addDocument(AddDocumentRequest $request): JsonResponse
    {
        $document = [
            'id' => $request->id,
            "name_combo" => $request->input('name_combo'),
        ];

        $elasticsearchDocument = ElasticsearchDocument::newIndexAndDocumentConstructor($request->input('index_name'), $document);
        $elasticsearchDocument->add();

        return response()->json(['document stored!']);
    }

    /**
     */
    public function addDocumentWithId(AddDocumentRequest $request, int $id): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->updateWithId(["initials" => $request->input('initials')], $id);

        return response()->json($response, $status);
    }


    /**
     * @throws GuzzleException
     */
    public function deleteDocument(DocumentRequest $request): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->deleteDocument($request->input('document_id'));

        return response()->json($response, $status);
    }

    /**
     * @throws GuzzleException
     */
    public function updateDocument(Request $request): void
    {
        ElasticsearchWithGuzzle::init()
            ->setIndexName($request->input('index_name'))
            ->setSearchField($request->input('document_name'))
            ->updateClientInitialsByDocId($request->document_id, $request->new_initials);
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
        $indexName = $request->input('index_name');
        $indicesOfElasticsearch = new IndicesOfElasticsearch($indexName);
        $indicesOfElasticsearch->store();

        return response()->json(['index created']);
    }

    public function deleteIndex(Request $request): JsonResponse
    {
        $indexName = $request->input('index_name');

        list($response, $status) = ElasticBuilder::init()
            ->deleteIndex($indexName);

        return response()->json($response, $status);
    }

    public function updateIndex(Request $request)//: JsonResponse
    {
        $indexName = $request->input('index_name');
        $settings = $request->settings;

        ElasticBuilder::init()
            ->deleteIndex($indexName);

        ElasticBuilder::init()
            ->createIndex($indexName, $settings);
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

    public function deleteIndexTotally(DeleteIndexRequest $request): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->deleteIndex();

        return response()->json($response, $status);
    }

    /**
     * @throws GuzzleException
     */
    public function indices(): JsonResponse
    {
        $indicesOfElasticsearch = IndicesOfElasticsearch::newWithNoArg();
        $indices = $indicesOfElasticsearch->indices();

        return response()->json(['indices' => $indices]);
    }

    public function countIndexDocuments(Request $request): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->countIndexDocuments();

        return response()->json($response, $status);
    }

    /**
     * @throws GuzzleException
     */
    public function qQuery(Request $request): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->qQuery($request->input('params'));

        return response()->json($response, $status);
    }

    /**
     * @throws GuzzleException
     */
    public function exactSearch(ExactSearchRequest $request): array
    {
        return ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->setSearchField($request->input('document_name'))
            // it extracts the array of results and sets to exactSearchResults which can be used like
            // exactSearchResults[0][_source']['initials']
            ->exactSearchAndSetResult($request->input('search_key'))
            ->getExactSearchResult();
    }

}
