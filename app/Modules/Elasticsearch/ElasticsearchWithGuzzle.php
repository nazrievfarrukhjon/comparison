<?php

namespace App\Modules\Elasticsearch;

use App\Modules\Blacklist\BlacklistESConfigs;
use App\Modules\Telegram\TelegramMessage;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ElasticsearchWithGuzzle
{
    private string $hostPort;

    private Guzzle $guzzle;

    private readonly array $headers;

    private array $result;

    private int $requestStatus;

    public function __construct(
    ) {
        $host = config('database.connections.elasticsearch.hosts');
        $this->hostPort = $host['scheme'] .
            '://' .
            $host['host'] . ':' .
            $host['port'];
        $this->guzzle = new Guzzle();
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    /**
     *      "settings": {
     *          "analysis": {
     *             "tokenizer": {
     *                  "trigram_tokenizer": {
     *                      "type": "ngram",
     *                      "min_gram": 3,
     *                      "max_gram": 3
     *                  }
     *              },
     *              "analyzer": {
     *                  "trigram_analyzer": {
     *                      "type": "custom",
     *                      "tokenizer": "trigram_tokenizer"
     *                  }
     *              }
     *          }
     *      },
     *      "mappings": {
     *          "properties": {
     *              "content": {
     *                  "type": "text",
     *                  "analyzer": "trigram_analyzer"
     *              }
     *          }
     *      }
     *
     * ==============
     * PUT /my-index-000001
     * {
     * "settings": {
     * "index": {
     * "number_of_shards": 3,
     * "number_of_replicas": 2
     * }
     * }
     * }
     *
     * @throws GuzzleException
     */
    public function createIndex(string $indexName, array $indexSettings): void
    {
        $this->guzzle->put(
            "{$this->hostPort}/{$indexName}",
            [
                'headers' => $this->headers,
                'json' => $indexSettings
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    public function health(): array
    {
        $response = $this->guzzle->get("{$this->hostPort}", [
            'headers' => $this->headers,
        ]);

        return [$response->getBody()->getContents(), 200];
    }

    /**
     * @throws GuzzleException
     */
    public function qQuery(string $uriSuffix): void
    {
        $response = $this->guzzle->get(
            "{$this->hostPort}/{$uriSuffix}",
            [
                'headers' => $this->headers,
            ]
        );

        $this->result = [$response->getBody()->getContents()];
    }

    /**
     * POST /my_index/_search
     * {
     *      "query": {
     *          "fuzzy": {
     *              "content": {
     *                  "value": "exaample",
     *                  "fuzziness": 2
     *              }
     *          }
     *      }
     * }
     *
     * ============
     * curl -X GET "localhost:9200/_search?pretty" -H 'Content-Type: application/json' -d'
     * {
     * "query": {
     * "fuzzy": {
     * "user.id": {
     * "value": "ki"
     * }
     * }
     * }
     * }
     * '
     *
     * @throws GuzzleException
     */
    public function fuzzySearch(): void
    {
        $response = $this->guzzle->post(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_search",
            [
                'headers' => $this->headers,
                'json' => [
                    "query" => [
                        "match" => [
                            "{$this->blacklistESConfigs->documentName()}" => [
                                "query" => $this->searchKey,
                                "fuzziness" => "AUTO:4,6",
                            ]
                        ]
                    ]
                ],
            ]
        );

        $this->result = [$response->getBody()->getContents(), 200];
    }


    /**
     * @throws GuzzleException
     */
    public function search(): void
    {
        $response = $this->guzzle->post(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_search",
            [
                'headers' => $this->headers,
                'json' => [
                    "query" => [
                        "match" => [
                            "{$this->blacklistESConfigs->documentName()}" => $this->constructSearchKey(
                                $this->searchKey
                            )
                        ]
                    ]
                ],
            ]
        );

        $this->result = [$response->getBody()->getContents(), 200];
    }

    /**
     * @throws GuzzleException
     */
    public function wildCardSearch(): void
    {
        $response = $this->guzzle->post(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_search",
            [
                'headers' => $this->headers,
                'json' => [
                    "query" => [
                        "wildcard" => [
                            "{$this->blacklistESConfigs->documentName()}" => [
                                "value" => '*' . $this->constructSearchKey(
                                        $this->searchKey
                                    ) . '*'
                            ]
                        ]
                    ]
                ],
            ]
        );

        $this->result = [$response->getBody()->getContents(), 200];
    }

    /**
     * @throws GuzzleException
     */
    public function add(array $document): void
    {
        $response = $this->guzzle->post(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_doc",
            [
                'headers' => $this->headers,
                'json' => $document
            ]
        );

        $this->result = [$response->getBody()->getContents(), 200];
    }

    /**
     * @throws GuzzleException
     */
    public function deleteDocument(string $documentId): void
    {
        $response = $this->guzzle->delete(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_doc/{$documentId}"
        );

        $this->result = [$response->getBody()->getContents(), 200];
    }

    /**
     * @throws GuzzleException
     */
    public function getAll(): void
    {
        $response = $this->guzzle->get(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_search",
            ['headers' => $this->headers]
        );

        $this->result = [$response->getBody()->getContents(), 200];

    }

    /**
     * @throws GuzzleException
     */
    public function deleteIndex(): void
    {

        $response = $this->guzzle->delete(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}"
        );

        $this->result = [$response->getBody()->getContents(), 200];

    }

    /**
     * @throws GuzzleException
     */
    public function indices(): array
    {
        $response = $this->guzzle->get(
            "{$this->hostPort}/_cat/indices"
        );

        return [$response->getBody()->getContents(), 200];
    }

    /**
     * @throws GuzzleException
     */
    public function countIndexDocuments(): void
    {
        $response = $this->guzzle->get(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_count"
        );

        $this->result = [$response->getBody()->getContents(), 200];
    }

    /**
     * @throws GuzzleException
     */
    public function findAndDeleteByDocId(array $item): void
    {
        $response = $this->guzzle->get(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_search",
            [
                'json' => [
                    'query' => [
                        'term' => $item,
                    ],
                ],
            ]
        );

        $blacklist = json_decode($response->getBody(), true);

        if ($blacklist['hits']['total']['value'] === 0) {
            Log::info(['no doc found by uid ', 'uid' => $item]);
        } else {
            foreach ($blacklist['hits']['hits'] as $hit) {
                $documentId = $hit['_id'];
                $this->guzzle->delete(
                    "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_doc/{$documentId}"
                );
            }
        }
    }

    /**
     * @throws GuzzleException
     */
    public function deleteDocByBlacklistId(string $BlacklistId): void
    {
        $response = $this->guzzle->get(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_search",
            [
                'json' => [
                    'query' => [
                        'term' => [
                            'Blacklist_id' => $BlacklistId,
                        ],
                    ],
                ],
            ]
        );

        $blacklist = json_decode($response->getBody(), true);

        if ($blacklist['hits']['total']['value'] === 0) {
            $telegram = new TelegramMessage(['body' => 'delete by Blacklist id. no doc found in ES by Blacklist id. ' . $BlacklistId]);
            $telegram->message();
            $telegram->send();
        } else {
            foreach ($blacklist['hits']['hits'] as $hit) {
                $documentId = $hit['_id'];
                $this->guzzle->delete(
                    "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_doc/{$documentId}"
                );
            }
        }
    }

    /**
     * it extracts the array of results
     * exactSearchResults[0][_source']['searchKey']
     *
     * @throws GuzzleException
     */
    public function exactSearchAndSetResult(string $searchKey): void
    {
        $response = $this->guzzle->get(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_search",
            [
                'json' => [
                    'query' => [
                        'term' => [
                            $this->searchField => $searchKey
                        ],
                    ],
                ],
            ]
        );

        $results = json_decode($response->getBody()->getContents(), true);

        if (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
            $this->result = $results['hits']['hits'];
        }
    }

    /**
     * @throws GuzzleException
     */
    public function updateClientSearchKeyByDocId(string $esDocId, string $newsearchKey): void
    {
        $this->guzzle->post(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_update/{$esDocId}",
            [
                'json' => [
                    'script' => [
                        'source' => 'ctx._source.searchKey = params.newsearchKey;',
                        'lang' => 'painless',
                        'params' => [
                            'newsearchKey' => $newsearchKey,
                        ],
                    ],
                ],
            ]
        );

    }


    /**
     * @throws GuzzleException
     */
    public function updateWithId(array $document, int $id): void
    {
        $response = $this->guzzle->post(
            "{$this->hostPort}/{$this->blacklistESConfigs->indexName()}/_doc/{$id}",
            [
                'headers' => $this->headers,
                'json' => $document
            ]
        );

        $this->result = [$response->getBody()->getContents(), 200];
    }

    private function constructSearchKey(string $searchKey)
    {
    }

    public function jsonResponse(): JsonResponse
    {
        return response()->json(['result' => $this->result], 200);
    }

}
