<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url:http,https', 'max:2048'],
            'slug' => ['nullable', 'alpha_dash', 'min:6', 'max:32', 'unique:links,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'slug taken, try another',
        ];
    }
}
