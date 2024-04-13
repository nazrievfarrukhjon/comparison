<?php

namespace App\Modules\Blacklist;

use App\Modules\Elasticsearch\ElasticsearchWithGuzzle;

class BlacklistEntity
{
    public function __construct(private string $searchKey)
    {

    }

    private array $similarity;

    public function find(): void
    {

        $elasticSearch = new ElasticsearchWithGuzzle();
        $elasticResponse = $elasticSearch->matched();
        $status = $elasticSearch->requestStatus();
        $this->similarity = $elasticResponse;

    }

    public function similarity(): array
    {
        return $this->similarity;
    }
}
