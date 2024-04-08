<?php

namespace App\Modules\Blacklist;

class BlacklistEntity
{
    private array $comparison;

    public function find(string $searchKey): void
    {

        $elasticSearch = new ElasticSearch(
            new ESRequest(
                new BlacklistES(
                    new SearchKeyword($searchKey)
                )
            )
        );

        $elasticResponse = $elasticSearch->matched();
        $status = $elasticSearch->requestStatus();
        $this->comparison = json_decode($elasticResponse, true);

    }
}
