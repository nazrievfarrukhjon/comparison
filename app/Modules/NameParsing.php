<?php

namespace App\Modules;

use App\Modules\Telegram\Telegram;

class NameParsing
{
    public static function init(): static
    {
        return (new static());
    }

    public function parseSpecialLatinSymbol(string $str): string
    {
        $characters = preg_split(
            '//',
            $str,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        if ($characters === false) {
            Telegram::query()->create(['body' => 'parsing error :' . $str]);
        }

        foreach ($characters as $key => $value) {
            if ($this->hasNonUTF8Characters($value)) {
                $char = mb_convert_encoding(
                    $value,
                    'UTF-8',
                    'ISO-8859-1'
                );
                $element = $this->convertToEnglish($char);
                $characters[$key] = $element;
            }
        }

        return implode('', $characters);
    }

    public function hasNonUTF8Characters(string $string): bool
    {
        return !mb_check_encoding($string, 'UTF-8');
    }

    private function convertToEnglish($char): string
    {
        $iso88591ToEnglish = [
            "Œ" => "OE",
            "œ" => "oe",
            '°' => '',
            '´' => '',
            "­" => "",
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'd',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ý' => 'y',
            'þ' => 'th',
            'ÿ' => 'y',
            'ß' => 'ss',
            'æ' => 'ae',
            'œ' => 'oe',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'AE',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'TH',
            'Ÿ' => 'Y',
        ];

        return $iso88591ToEnglish[$char];
    }

}
