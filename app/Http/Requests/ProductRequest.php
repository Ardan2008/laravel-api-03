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
        // PUT mengirim form utuh, PATCH boleh sebagian field saja
        $required = $this->isMethod('PATCH') ? 'sometimes|required' : 'required';

        return [
            'id_kategori' => $required.'|exists:kategoris,id',
            'name' => $required.'|string|max:255',
            'price' => [$required, 'integer', 'min:0'],
            'description' => 'sometimes|nullable|string',
            'stock' => $required.'|integer|min:0',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}