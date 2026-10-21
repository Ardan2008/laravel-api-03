<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_kategori' => $this->id_kategori,
            'kategori' => $this->kategori?->name,
            'name' => $this->name,
            'price' => (int) $this->price,
            'description' => $this->description,
            'stock' => $this->stock,
            'image' => $this->image,
            // pakai url() supaya ikut host+port request, bukan APP_URL yang bisa berbeda
            'image_url' => $this->image ? url('/storage/' . $this->image) : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}