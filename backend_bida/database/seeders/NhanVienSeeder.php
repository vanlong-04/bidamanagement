<?php

namespace Database\Seeders;

use App\Models\NhanVien;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NhanVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NhanVien::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('123456'),
                'full_name' => 'Admin User',
                'email' => 'admin@example.com',
                'phone' => '0123456789',
                'hire_date' => now(),
                'status' => 'active'
            ]
        );

        NhanVien::firstOrCreate(
            ['username' => 'nhanvien'],
            [
                'password' => Hash::make('123456'),
                'full_name' => 'Nhân Viên Test',
                'email' => 'nhanvien@example.com',
                'phone' => '0987654321',
                'hire_date' => now(),
                'status' => 'active'
            ]
        );
    }
}
