<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|max:100|min:3',
            'email' => 'email|max:100|unique:users',
            'password' => 'min:8|max:100'
        ];
    }
}
