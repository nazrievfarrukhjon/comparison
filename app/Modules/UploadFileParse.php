<?php

namespace App\Modules\blacklistorists\UseCases\Uploader\UploaderParsers;

use App\Modules\Parsers\StrCleaner;
use App\Modules\Parsers\StrParser;
use App\Modules\Telegram\Telegram;
use App\Modules\blacklistorists\Models\blacklistorist;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithStartRow;

class UploadFileParse implements ToArray, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function array(array $rows): void
    {
        foreach ($rows as $row) {
            $cyrillicInitials = StrCleaner::removeNonCyrillic(trim($row[0]));
            $cyrillicInitialsArray = explode(' ', $cyrillicInitials);

            if (count($cyrillicInitialsArray) > 1) {
                $this->addblacklistorist($cyrillicInitialsArray, $row);
            }
        }
    }

    private function constructFourthName(...$initials): ?string
    {
        $fourthName = '';

        foreach ($initials as $initial) {
            $fourthName .= $initial;
        }

        return strlen($fourthName) > 0 ? $fourthName : null;
    }

    private function addblacklistorist($cyrillicInitialsArray, $row): void
    {
        try {
            $concatenatedNames = StrParser::concatenateInitials(
                $cyrillicInitialsArray[0],
                $cyrillicInitialsArray[1],
                $cyrillicInitialsArray[2] ?? null,
                $this->constructFourthName(
                    $cyrillicInitialsArray[3] ?? '',
                    $cyrillicInitialsArray[4] ?? '',
                    $cyrillicInitialsArray[5] ?? '',
                    $cyrillicInitialsArray[6] ?? '',
                    $cyrillicInitialsArray[7] ?? '',
                    $cyrillicInitialsArray[8] ?? '')
            );

            $blacklistorist = new blacklistorist([
                'concatenated_names' => $concatenatedNames,
                'second_name' => $cyrillicInitialsArray[0] ?? null,
                'first_name' => $cyrillicInitialsArray[1] ?? null,
                'third_name' => $cyrillicInitialsArray[2] ?? null,
                'fourth_name' => $this->constructFourthName(
                    $cyrillicInitialsArray[3] ?? '',
                    $cyrillicInitialsArray[4] ?? '',
                    $cyrillicInitialsArray[5] ?? '',
                    $cyrillicInitialsArray[6] ?? '',
                    $cyrillicInitialsArray[7] ?? '',
                    $cyrillicInitialsArray[8] ?? ''),
                //international police
                'organization' => 'IP',
                'birth_date' => Carbon::parse($row[1]),
                //comment it
                //'others'  => 'srochnooo! nbt',
            ]);

            $blacklistorist->save();

        } catch (\Exception $e) {
            Log::info(['$e' => $e->getMessage(), '$concatenatedNames' => $concatenatedNames]);
            Telegram::query()->create(['body' => 'add ip blacklist to pgsql error']);
        }
    }

}
