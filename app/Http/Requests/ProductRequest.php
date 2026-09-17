<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_kategori' => 'required|exists:kategoris,id',
            'name' => 'required|string|max:255',
            'price' => ['required', 'regex:/^\d{1,3}(\.\d{3})*$/'],
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
        ];
    }
}