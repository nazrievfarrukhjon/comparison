<?php

namespace App\Modules\Whitelist\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareToBlacklistRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search_key' => 'required|string|max:100|min:3',
            'operation_type' => 'required|string|max:255',
            'external_uuid' => 'required',
            'date_of_birth' => 'required',
        ];
    }
}
