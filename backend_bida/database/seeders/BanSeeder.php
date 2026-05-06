<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ
        DB::table('bans')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('bans')->insert([
            [
                'ban_name' => 'MR. SUNG 1',
                'loai_ban' => '1',
                'status' => 1,
            ],
            [
                'ban_name' => 'MR. SUNG 2',
                'loai_ban' => '1',
                'status' => 1,
            ],
            [
                'ban_name' => 'MR. SUNG 3',
                'loai_ban' => '1',
                'status' => 1,
            ],
            [
                'ban_name' => 'MR. SUNG 4',
                'loai_ban' => '1',
                'status' => 1,
            ],
            [
                'ban_name' => 'MR. SUNG 5',
                'loai_ban' => '1',
                'status' => 1,
            ],
            [
                'ban_name' => 'MR. SUNG 6',
                'loai_ban' => '1',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 1',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 2',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 3',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 4',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 5',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 6',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 7',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 8',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'POOL SPACE-X 9',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'VIP RASSON 1',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'VIP RASSON 2',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'VIP RASSON 3',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'VIP RASSON 4',
                'loai_ban' => '2',
                'status' => 1,
            ],
            [
                'ban_name' => 'VIP RASSON 5',
                'loai_ban' => '2',
                'status' => 1,
            ],
        ]);
    }
}
