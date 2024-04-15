<?php

namespace App\Modules;

class InputAttributes
{
    private array $attributes;

    public static function newStringConstructor(): InputAttributes
    {

    }

    public static function newArrayConstructor(array $attributes): InputAttributes
    {
        $obj = new self();
        $obj->attributes = $attributes;
        return $obj;
    }

    public static function newIntArrayConstructor(): InputAttributes
    {

    }

    public static function newStringArrayConstructor(): InputAttributes
    {

    }

    public static function newIntStringConstructor(): InputAttributes
    {

    }

    public function searchKey(): string
    {
        $string = MyString::newWordConstructor($this->attributes['search_key']);
        return $string->cleanedKey();
    }

    public function indexName(): string
    {
        return $this->attributes['index_name'];
    }

    public function documentField(): string
    {
        return $this->attributes['document_field'];

    }
}
