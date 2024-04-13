<?php

namespace App\Modules\Elasticsearch\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddDocumentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'index_name' => 'required|string|max:30',
            'name_combo' => 'required|string|max:70',
            'id' => 'required|integer'
        ];
    }
}
