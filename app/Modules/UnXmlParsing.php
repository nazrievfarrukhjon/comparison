<?php

namespace App\Modules;

use App\Modules\Blacklist\Models\Blacklist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UNBlacklistXmlListParing
{
    public function perform(Request $request): void
    {
        $string = file_get_contents($request->file('file')->getPathname());
        $xml = new \SimpleXMLElement($string);

        try {
            foreach ($xml->xpath('//INDIVIDUALS/INDIVIDUAL') as $t) {
                if (isset($t->children()->INDIVIDUAL_DATE_OF_BIRTH)) {
                    $this->iterateData($t);
                } else {
                    $this->storeblacklistorist($t);
                }
            }
        } catch (\Exception $e) {
            Log::info('qwe', ['class UNBlacklistXmlListParser' => $e]);
        }

    }

    private function iterateData($t): void
    {
        foreach ($t->children()->INDIVIDUAL_DATE_OF_BIRTH as $a) {
            $birth_date = $this->parseBirthDate($a);
            foreach ($birth_date as $b) {
                $this->storeblacklistorist($t, $b);
            }
        }
    }

    private function storeblacklistorist($t, $b = null): void
    {
        $concatenatedNames = StrParser::concatenateInitials(
            trim($t->children()->FIRST_NAME),
            trim($t->children()->SECOND_NAME),
            trim($t->children()->THIRD_NAME),
            trim($t->children()->FOURTH_NAME)
        );

        $blacklistorist = new Blacklist([
            'concat_names' => $concatenatedNames,
            'first_name' => $t->children()->FIRST_NAME,
            'second_name' => $t->children()->SECOND_NAME,
            'third_name' => isDataValid($t->children()->THIRD_NAME) ? $t->children()->THIRD_NAME : null,
            'fourth_name' => isDataValid($t->children()->FOURTH_NAME) ? $t->children()->FOURTH_NAME : null,
            'type' => 'UN',
            'date_of_birth' => $b,
            'others' => 'un_unhandled',
        ]);

        $blacklistorist->save();
    }

    private function parseBirthDate($a): array
    {
        $arrayOfBirthDates = [];
        if ($a->TYPE_OF_DATE == 'BETWEEN') {
            $arrayOfBirthDates[] = $a->FROM_YEAR;
            $arrayOfBirthDates[] = $a->TO_YEAR;
        }
        if ($a->TYPE_OF_DATE == 'APPROXIMATELY') {
            if (isset($a->DATE)) {
                $arrayOfBirthDates[] = $a->DATE;
            }
            if (isset($a->YEAR)) {
                $arrayOfBirthDates[] = $a->YEAR;
            }
        }
        if ($a->TYPE_OF_DATE == 'EXACT') {
            if (isset($a->DATE)) {
                $arrayOfBirthDates[] = $a->DATE;
            }
            if (isset($a->YEAR)) {
                $arrayOfBirthDates[] = $a->YEAR;
            }
        }
        if (isset($a->NOTE) && preg_match('/[0-9]{4}/', $a->NOTE)) {
            $tempBirthDateList = [];
            $years = preg_replace('/[^0-9]/', '', $a->NOTE);
            $l = 0;
            for ($i = 0; $i < count(str_split($years)); $i += 4) {
                $length = $l + 4;
                $tempBirthDateList[] = substr($years, $i, $length);
            }
            $arrayOfBirthDates = array_merge_recursive($arrayOfBirthDates, $tempBirthDateList);
        }

        return $this->parseForXmlDate($arrayOfBirthDates);
    }

    private function parseForXmlDate($arrayOfBirthDates): array
    {
        $tempArr = [];
        try {
            foreach ($arrayOfBirthDates as $date) {
                $tempArr[] = Carbon::parse(strlen($date) > 4 ? $date : '12-12-' . $date)
                    ->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $tempArr[] = '12-12-1888';
        }
        return $tempArr;
    }

}
