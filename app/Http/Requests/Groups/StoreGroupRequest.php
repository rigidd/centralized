<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must not be greater than 255 characters.',
        ];
    }
}
