<?php

namespace App\Models;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id_kategori',
        'name',
        'price',
        'stock',
        'description',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    protected function price(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                $digits = preg_replace('/\D/', '', $value);

                return (int) ($digits === null || $digits === '' ? 0 : $digits);
            },
        );
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }
}