<?php

namespace App\Modules\ElasticSearch\Controllers;

use App\Modules\Elastic\ElasticBuilder;
use App\Modules\Elastic\ElasticService;
use App\Modules\Elastic\Requests\AddDocumentRequest;
use App\Modules\Elastic\Requests\CreateIndexRequest;
use App\Modules\Elastic\Requests\DeleteIndexRequest;
use App\Modules\Elastic\Requests\DocumentRequest;
use App\Modules\Elastic\Requests\ElasticSearchRequest;
use App\Modules\Elastic\Requests\ExactSearchRequest;
use App\Modules\Parsers\StrConverter;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ElasticController
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
            'uid' => $request->uid ?? rand(),
            "initials" => $request->input('initials'),
        ];

        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->add($document);

        return response()->json($response, $status);
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
        ElasticBuilder::init()
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
        $settings = ElasticService::constructTrigramIndexSetting();

        list($response, $status) = ElasticBuilder::init()
            ->createIndex($indexName, $settings);

        return response()->json($response, $status);
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

    public function getAllFromIndex(Request $request)//: JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->getAll();

        return response()->json($response, $status);
    }

    public function deleteIndexTotally(DeleteIndexRequest $request): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName($request->input('index_name'))
            ->deleteIndex();

        return response()->json($response, $status);
    }

    public function getAllIndex(): JsonResponse
    {
        list($response, $status) = ElasticBuilder::init()
            ->getAllIndices();

        return response()->json($response, $status);
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
