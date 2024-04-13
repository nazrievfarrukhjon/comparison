<?php

namespace App\Modules\Elasticsearch\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'index_name' => 'required|string|max:30'
        ];
    }
}
