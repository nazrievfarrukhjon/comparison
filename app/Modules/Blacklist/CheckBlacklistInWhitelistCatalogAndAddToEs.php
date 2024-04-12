<?php

namespace App\Modules\Blacklist\UseCases;

use App\Modules\Clients\Helpers\ClientStatusEnum;
use App\Modules\Elastic\ElasticBuilder;
use App\Modules\RabbitMQ\RabbitMqService;
use App\Modules\Telegram\TelegramMessage;
use App\Modules\Blacklist\Models\Blacklist;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\EuBlacklistPermutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\IPBlacklistPermutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\KzBlacklistPermutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\MiaBlacklistPermutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\NbtListBlacklistPermutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\OfacBlacklistPermutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\Permutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\RefBlacklistPermutation;
use App\Modules\Blacklist\UseCases\BlacklistPermutations\UnBlacklistPermutation;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CheckBlacklistInWhitelistCatalogAndAddToEs
{

    private array $permutations
        = [
            'UN' => UnBlacklistPermutation::class,
            'MIA' => MiaBlacklistPermutation::class,
            'IP' => IPBlacklistPermutation::class,
            'REF' => RefBlacklistPermutation::class,
        ];

    public static function init(): CheckPgBlacklistsInClientsCatalogAndAddToElasticUseCase
    {
        return new CheckPgBlacklistsInClientsCatalogAndAddToElasticUseCase();
    }

    /**
     */
    public function perform(): void
    {
        while (true) {
            $Blacklists = Blacklist::query()
                ->where('is_checked', false)
                ->chunkById(10, function ($Blacklist) {
                    foreach ($Blacklist as $Blacklist) {
                        /**
                         * @var Permutation $permutation
                         */
                        $permutation
                            = $this->permutations[$Blacklist->organization]::init();
                        $BlacklistInitialsArr
                            = $permutation->permuteBlacklistInitials($Blacklist);

                        foreach ($BlacklistInitialsArr as $BlacklistLatinInitial) {
                            if (preg_match('/[А-Яа-я]/', $BlacklistLatinInitial)) {
                                TelegramMessage::query()->create([
                                    'body' => 'inits must not have cyrillic letter:'
                                        . $BlacklistLatinInitial
                                ]);
                            }

                            $this->addToElastic($Blacklist, $BlacklistLatinInitial);

                            // sendToMassQueue bcz new main passport check can be blocked by regular queue
                            $this->checkBlacklistInClientsAndReportToMassQueue($Blacklist,
                                $BlacklistLatinInitial);
                        }

                        $Blacklist->is_checked = true;
                        $Blacklist->save();
                        dd('end');
                    }
                });
            sleep(60);
        }
    }


    /**
     * @throws GuzzleException
     */
    private function addToElastic(
        Blacklist $Blacklist,
        string    $BlacklistInitial
    ): void
    {
        $document = [
            'initials' => $BlacklistInitial,
            'org_type' => $Blacklist->organization,
            'uid' => $Blacklist->external_uid,
            'Blacklist_id' => $Blacklist->id,
        ];

        ElasticBuilder::init()
            ->setIndexName('Blacklists')
            ->add($document);
    }

    /**
     * todo use exchange for rabbit
     *
     * @throws Exception|GuzzleException
     *  sendToMassQueue bcz new main passport check can be blocked by regular queue
     *
     */
    public function checkBlacklistInClientsAndReportToMassQueue(
        Blacklist $Blacklist,
        string    $BlacklistLatinInitial
    ): void
    {
        if (mb_detect_encoding($BlacklistLatinInitial, 'UTF-8')) {
            $this->handleBlacklistVsClientCheckingAndReport($BlacklistLatinInitial,
                $Blacklist->id, $Blacklist->organization);
        } else {
            TelegramMessage::query()->create([
                'body' => 'wrong encoding to check Blacklist in  clients'
                    . $BlacklistLatinInitial
            ]);
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     * sendToMassQueue bcz new main passport check can be blocked by regular queue
     */
    private function handleBlacklistVsClientCheckingAndReport(
        $latinInitCombo,
        int $BlacklistId,
        string $BlacklistType
    ): void
    {
        list($response, $status) = ElasticBuilder::init()
            ->setIndexName('clients')
            ->fuzzySearch('initials', $latinInitCombo);

        $result = json_decode($response, true);

        if (config('environments.NOTIFICATION_ENABLED')) {
            if (isset($result['hits']['total']['value'])
                && $result['hits']['total']['value'] > 0
            ) {
                $ClientsEsDocs = $result['hits']['hits'];

                $Blacklist = Blacklist::query()
                    ->where('id', $BlacklistId)
                    ->first();

                foreach ($ClientsEsDocs as $ClientEsDoc) {
                    Log::channel('elastic')
                        ->info([
                            'always: CheckPgBlacklistsInClientsCatalogAndAddToElasticCommand:  client vs Blacklist search result',
                            $ClientEsDoc
                        ]);
                    //1
                    RabbitMqService::init()
                        ->constructMsgForAboutSearchResults(
                            $ClientEsDoc['_id'],
                            [$Blacklist]
                        )
                        ->sendToMassQueue();

                    //2
                    RabbitMqService::init()
                        ->setClientStatus(
                            ClientStatusEnum::Blacklist->value
                        )
                        ->setClientId(
                            $ClientEsDoc['_id']
                        )
                        ->constructSmsForClientStatusChanging()
                        ->sendToMassQueue();

                    //3
                    $this->notifyTelegram(
                        $latinInitCombo,
                        $Blacklist,
                        $ClientEsDoc['_id'],
                        $BlacklistType,
                    );
                }
            }
        }
    }

    private function notifyTelegram(
        string    $latinInitCombo,
        Blacklist $Blacklist,
        int       $ClientId,
                  $BlacklistType,
    ): void
    {
        $message = " - Blacklist vs clients checking in es. \n" .
            '- искали :  ' . $latinInitCombo . ".\n" .
            '- нашли : ' . $Blacklist->concatenated_names . "\n" .
            '- тип: ' . $BlacklistType . "\n" .
            config('environments._FRONT_URL') . 'clients/' . $ClientId
            . '/main';

        TelegramMessage::query()
            ->create(['body' => $message]);
    }
}
