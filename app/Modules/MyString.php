<?php

namespace App\Modules;

use Illuminate\Support\Str;

class MyString
{
    private string $word;

    public static function newWordConstructor(string $word): MyString
    {
        $obj = new self();
        $obj->word = $word;
        return $obj;
    }

    public function cleanedKey(): string
    {
        $latinWord = $this->convertCyrillicToLatin();
        return Str::lower(preg_replace('/[^a-zA-Z]/', '', $latinWord));
    }

    private function convertCyrillicToLatin(): string
    {
        $cyrillicLetters = [
            'А', 'Ә', 'Б', 'В', 'Г', 'Ғ', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Қ', 'Л', 'М', 'Н', 'О', 'П',
            'Р', 'С', 'Т', 'У', 'Ұ', 'Ү', 'Ф', 'Х', 'Ҳ', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'І', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'ә', 'б', 'в', 'г', 'ғ', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'қ', 'л', 'м', 'н', 'о', 'п',
            'р', 'с', 'т', 'у', 'ұ', 'ү', 'ф', 'х', 'ҳ', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'і', 'ь', 'э', 'ю', 'я',
            'Ѐ', 'Ѝ', 'І', 'Ѣ', 'Ѳ', 'Ѵ',
            'ҷ', 'Ҷ', 'Ў', 'ў', 'ӣ', 'Ӣ', 'ӯ'
        ];

        $latinLetters = [
            'A', 'A', 'B', 'V', 'G', 'GH', 'D', 'E', 'YO', 'ZH', 'Z', 'I', 'Y', 'K', 'Q', 'L', 'M', 'N', 'O', 'P',
            'R', 'S', 'T', 'U', 'U', 'U', 'F', 'KH', 'H', 'TS', 'CH', 'SH', 'SHCH', '', 'Y', 'I', '', 'E', 'YU', 'YA',
            'a', 'a', 'b', 'v', 'g', 'gh', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'q', 'l', 'm', 'n', 'o', 'p',
            'r', 's', 't', 'u', 'u', 'u', 'f', 'kh', 'h', 'ts', 'ch', 'sh', 'shch', '', 'y', 'i', '', 'e', 'yu', 'ya',
            'E', 'I', 'I', 'IE', 'F', 'I',
            'j', 'J', 'u', 'U', 'i', 'I', 'u',
        ];

        return str_replace($cyrillicLetters, $latinLetters, $this->word);
    }
}
