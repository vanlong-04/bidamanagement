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
        Schema::create('hoa_dons', function (Blueprint $table) {
            $table->id('hoa_don_id');
            $table->unsignedBigInteger('ban_id');
            $table->unsignedBigInteger('nhan_vien_id')->nullable();;
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->decimal('charge', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->enum('status', ['chưa thanh toán', 'đã thanh toán'])->default('chưa thanh toán');
            $table->string('payment_method', 50)->nullable();
            $table->dateTime('expected_end_time')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->timestamps();

            $table->foreign('ban_id')->references('ban_id')->on('bans')->onDelete('cascade');
            $table->foreign('nhan_vien_id')->references('nhan_vien_id')->on('nhan_viens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_dons');
    }
};
