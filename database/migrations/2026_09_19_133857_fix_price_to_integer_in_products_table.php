<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('products')->select('id', 'price')->get()->each(function ($product) {
            $digits = preg_replace('/\D/', '', (string) $product->price);

            if ($digits !== null && $digits !== '') {
                DB::table('products')->where('id', $product->id)->update([
                    'price' => (int) $digits,
                ]);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('price')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('price', 255)->change();
        });

        DB::table('products')->select('id', 'price')->get()->each(function ($product) {
            $digits = preg_replace('/\D/', '', (string) $product->price);

            if ($digits !== null && $digits !== '') {
                DB::table('products')->where('id', $product->id)->update([
                    'price' => number_format((int) $digits, 0, ',', '.'),
                ]);
            }
        });
    }
};