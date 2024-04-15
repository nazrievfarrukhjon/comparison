<?php

namespace App\Modules\Elasticsearch\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'index_name' => 'required|string|max:30',
            'es_doc_id' => 'required|string|max:30',
        ];
    }
}
