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

    protected function price(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => $this->normalizeRupiah($value),
        );
    }

    private function normalizeRupiah(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value);

        if ($digits === null || $digits === '') {
            return '0';
        }

        return number_format((int) $digits, 0, ',', '.');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }
}