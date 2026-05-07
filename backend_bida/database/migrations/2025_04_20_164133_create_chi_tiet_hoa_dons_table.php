<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chi_tiet_hoa_dons', function (Blueprint $table) {
            $table->id('chi_tiet_hoa_don_id');
            $table->unsignedBigInteger('hoa_don_id');
            $table->unsignedBigInteger('dich_vu_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
            $table->foreign('hoa_don_id')->references('hoa_don_id')->on('hoa_dons')->onDelete('cascade');
            $table->foreign('dich_vu_id')->references('dich_vu_id')->on('dich_vus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_hoa_dons');
    }
};
