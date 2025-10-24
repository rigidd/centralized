<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
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
