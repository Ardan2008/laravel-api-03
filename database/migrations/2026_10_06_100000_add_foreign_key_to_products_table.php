<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // restrict: kategori yang masih dipakai produk tidak boleh dihapus
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('id_kategori')
                ->references('id')
                ->on('kategoris')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['id_kategori']);
        });
    }
};