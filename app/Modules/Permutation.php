<?php

namespace App\Modules;

use App\Modules\Parsers\StrParser;
use App\Modules\Telegram\Telegram;
use Illuminate\Support\Facades\Log;

class Permutation
{
    public function __construct(private readonly blacklistParserService $parserService) {}

    public static function perform(array $initials): array
    {
        return (new static(new blacklistParserService()))->performDetailedPermutations($initials);
    }

    /**
     * permute all possible combination including partially
     * a b c
     * c b a
     * b c a
     * a b
     * b a
     * c a
     * a c
     */
    private function performDetailedPermutations(array $initials): array
    {
        if ($this->parserService->hasNonEnglish($initials)) {
            Log::info('please manually correct and add it to ES', $initials);

            $msg = 'word has non english symbol correct manually ' . json_encode($initials);

            Telegram::query()
                ->create(['body' => $msg]);

        } else {
            $initials = $this->parserService->cleanArrOfInitsFromNonEnglishLetters($initials);
            return $this->permuteInitialsList($initials);
        }

        return [];
    }

    public function permuteInitialsList(array $initialsList): array
    {
        $length = count($initialsList);

        if ($length === 1) {
            return $initialsList;
        } elseif ($length === 2) {
            return $this->permuteTwoInitials($initialsList);
        } elseif ($length === 3) {
            return $this->permuteThreeInitials($initialsList);
        } elseif ($length === 4) {
            return $this->permuteFourInitials($initialsList);
        } elseif ($length === 5) {
            return $this->permuteFiveInitials($initialsList);
        } elseif ($length < 15) {
            return $this->permuteFiveInitials($initialsList);
        }

        $this->smsAndLog($initialsList);

        return [];
    }

    private static function permuteFourInitials(array $initials): array
    {
        $fourInits = StrParser::wordCombos($initials);
        $permuteOfA = self::permuteThreeInitials([$initials[0], $initials[1], $initials[2]]);
        $permuteOfB = self::permuteThreeInitials([$initials[0], $initials[1], $initials[3]]);
        $permuteOfC = self::permuteThreeInitials([$initials[0], $initials[2], $initials[3]]);
        $permuteOfD = self::permuteThreeInitials([$initials[1], $initials[2], $initials[3]]);

        return array_merge_recursive($fourInits, $permuteOfA, $permuteOfB, $permuteOfC, $permuteOfD);
    }

    private static function permuteTwoInitials(array $initials): array
    {
        return StrParser::wordCombos($initials);
    }

    private static function permuteThreeInitials(array $initials): array
    {
        $comboOfThreeInitials = StrParser::wordCombos($initials);
        $comboOfFirstSecond = self::permuteTwoInitials([
            $initials[0],
            $initials[1]
        ]);
        $comboOfFirstThird = self::permuteTwoInitials([
            $initials[0],
            $initials[2]
        ]);
        $comboOfSecondThird = self::permuteTwoInitials([
            $initials[1],
            $initials[2]
        ]);

        return array_merge($comboOfThreeInitials, $comboOfFirstSecond, $comboOfFirstThird, $comboOfSecondThird);
    }


    private function permute(array $initials): string
    {
        $perms = $this->permuteHelper($initials);

        $permutations = implode('', $perms[0]);

        for ($i = 1; $i < count($perms); $i++) {
            $permutations .= ' ' . implode('', $perms[$i]);
        }

        return $permutations;
    }

    private function permuteHelper(array $initials): array
    {
        if (count($initials) == 1) {
            return [$initials];
        }

        $result = [];
        foreach ($initials as $key => $value) {
            $copy = $initials;
            unset($copy[$key]);
            $subPerms = $this->permuteHelper($copy);
            foreach ($subPerms as $subPerm) {
                $result[] = array_merge([$value], $subPerm);
            }
        }

        return $result;
    }

    private function permuteFiveInitials(array $initials): array
    {
        $fiveInits = StrParser::wordCombos($initials);
        $permuteOfA = self::permuteFourInitials([$initials[0], $initials[1], $initials[2], $initials[3]]);
        $permuteOfB = self::permuteFourInitials([$initials[0], $initials[1], $initials[2], $initials[4]]);
        $permuteOfC = self::permuteFourInitials([$initials[0], $initials[1], $initials[3], $initials[4]]);
        $permuteOfD = self::permuteFourInitials([$initials[0], $initials[2], $initials[3], $initials[4]]);
        $permuteOfE = self::permuteFourInitials([$initials[1], $initials[2], $initials[3], $initials[4]]);

        return array_merge_recursive(
            $fiveInits,
            $permuteOfA,
            $permuteOfB,
            $permuteOfC,
            $permuteOfD,
            $permuteOfE,
        );
    }

    private function smsAndLog(array $initialsList): void
    {
        $msg = 'more than 15 initials is crucial for ES performance '
            .json_encode($initialsList);

        Log::info($msg);

        Telegram::query()
            ->create(['body' => $msg]);
    }

}
