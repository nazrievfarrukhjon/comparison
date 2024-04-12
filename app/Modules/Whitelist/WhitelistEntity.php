<?php

namespace App\Modules\Whitelist;


class WhitelistEntity
{
    public function __construct(private string $searchKey)
    {
    }

    private array $similarity;

    public function find(): void
    {
        $searchKey = new WhitelistSearchKey($this->searchKey);
        $searchKey->parse();
        $searchKey = $searchKey->parsedSearchKey();

        //how to use composition best practice
        $elasticSearch = new WhitelistElasticSearch($searchKey);
        $elasticSearch->search();
        $this->similarity = $elasticSearch->parsedResult();
    }

    public function similarity(): array
    {
        return $this->similarity;
    }
}
