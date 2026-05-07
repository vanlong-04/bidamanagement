<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nhan_viens', function (Blueprint $table) {
            $table->id('nhan_vien_id');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('full_name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->nullable();
            $table->date('hire_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Thêm tài khoản admin mặc định
        DB::table('nhan_viens')->insert([
            'username' => 'admin',
            'password' => Hash::make('123456'),
            'full_name' => 'Administrator',
            'email' => 'admin@example.com',
            'phone' => '0123456789',
            'hire_date' => now(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_viens');
    }
};
