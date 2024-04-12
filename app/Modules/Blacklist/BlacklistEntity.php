<?php

namespace App\Modules\Blacklist;

class BlacklistEntity
{
    public function __construct(private string $searchKey)
    {

    }

    private array $similarity;

    public function find(): void
    {

        $elasticSearch = new ElasticSearch(
            new ESRequest(
                new BlacklistES(
                    new SearchKeyword($this->searchKey)
                )
            )
        );

        $elasticResponse = $elasticSearch->matched();
        $status = $elasticSearch->requestStatus();
        $this->similarity = $elasticResponse;

    }

    public function similarity(): array
    {
        return $this->similarity;
    }
}
