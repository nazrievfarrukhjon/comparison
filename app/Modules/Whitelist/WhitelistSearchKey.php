<?php

namespace App\Modules\Whitelist;


use Illuminate\Support\Str;

class WhitelistSearchKey
{
    public function __construct(private string $searchKey)
    {
    }

    public function parsed(): string
    {
        $this->cleanFromNonAlphabeticCharacters();
        $this->lowerAndRemoveDoubleSpaces();

        return $this->searchKey;
    }

    public function parsedSearchKey(): string
    {
    }

    private function cleanArrOfInitsFromNonEnglishLetters(array $initials): array
    {
        $cleanedInitials = [];

        foreach ($initials as $i) {
            $cleanedInitials[] = $this->cleanFromNonAlphabeticCharacters($i);
        }

        return $cleanedInitials;
    }

    private function cleanFromNonAlphabeticCharacters(): string
    {
        return preg_replace(
            '/[-!$%^&*()_+|~=`{}\[\]:";\'<>?,.\/ 0-9№’‘»«”“]/',
            '',
            $this->searchKey
        );
    }

    private function hasNonEnglish(array $initials): bool
    {
        foreach ($initials as $init) {
            if (preg_match('/[^\x20-\x7E]/', $init)) {
                return true;
            }
        }

        return false;
    }

    private function lowerAndRemoveDoubleSpaces(): void
    {
       $this->searchKey =  str_replace('  ', ' ', Str::lower($this->searchKey));
    }

    private function replaceControlCharsWithSpace(): void
    {
        $this->searchKey = str_replace('\t', ' ', $this->searchKey);
    }

    private function removeNonEnglishLetters(): static
    {
        $this->searchKey = preg_replace('/[^a-zA-Z]/', '', $this->searchKey);
    }

    private function leaveOnlySpaceButRemoveNonEnglishLetters(): string
    {
        $this->searchKey = preg_replace(
            '/[-!$%^&*()_+|~=`{}\[\]:";\'<>?,.\/0-9№’‘»«”“]/',
            '',
            $this->searchKey
        );
    }

    private function removeSpecialCases(): string
    {
        $this->searchKey = preg_replace('/[0-9]/', '', $this->searchKey);
        $this->searchKey = str_replace('NO.', '', $this->searchKey);
        $this->searchKey = str_replace('DE LA O', 'DELAO', $this->searchKey);
    }

    private function concatSingleChars(): void
    {
        $words = explode(' ', $this->searchKey);
        $result = '';

        $tempString = '';

        foreach ($words as $word) {
            if (strlen($word) === 1) {
                $tempString .= $word;
            } else {
                $result .= strlen($tempString) === 1 ? $tempString : ' ' . $tempString;
                $tempString = '';
                $result .= ' ' . $word;
            }
        }

        $result .= strlen($tempString) === 1 ? $tempString : '';

        $this->searchKey = ltrim($result);
    }

    private function convertCyrToLatin(): void
    {
        $this->searchKey = StrConverter::convertCyrillicToLatin($this->str);
    }

}
