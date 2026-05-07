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
        Schema::create('dat_bans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ban_id')->nullable(); // Có thể đặt bàn cụ thể hoặc chỉ đặt loại bàn
            $table->integer('loai_ban')->default(1); // 1: Thường, 2: VIP
            $table->string('ten_khach_hang');
            $table->string('so_dien_thoai');
            $table->dateTime('thoi_gian_dat');
            $table->integer('so_luong_nguoi')->default(1);
            $table->string('ghi_chu')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dat_bans');
    }
};
