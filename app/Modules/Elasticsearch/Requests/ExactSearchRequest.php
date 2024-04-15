<?php

namespace App\Modules\Elasticsearch\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExactSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'document_name' => 'required|string|max:20',
            'index_name' => 'required|string|max:20',
            'search_key' => 'required|string|max:70',
        ];
    }
}
