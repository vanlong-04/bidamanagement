<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DichVuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Xóa dữ liệu cũ
        DB::table('dich_vus')->truncate();
        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::table('dich_vus')->insert([
            ['dich_vu_name' => 'Mr Sung', 'price' => 60000, 'loai_dich_vu' => 'gio'],
            ['dich_vu_name' => 'SpaceX', 'price' => 50000, 'loai_dich_vu' => 'gio'],
            ['dich_vu_name' => 'Rasson', 'price' => 70000, 'loai_dich_vu' => 'gio'],
            ['dich_vu_name' => 'Trà xanh 0 độ', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Tiger bạc', 'price' => 25000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Huda', 'price' => 18000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Larue', 'price' => 17000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Heineken', 'price' => 28000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Larue Smooth (180ml)', 'price' => 18000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Tiger xanh', 'price' => 22000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Strongbow úp ngược', 'price' => 49000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Coca Cola', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Revive trắng', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Nước khoáng', 'price' => 15000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Pepsi', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Nutriboost vị dâu', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Nước yến', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Red bull', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Sprite', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Nước suối', 'price' => 15000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Sting dâu', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Sting vàng', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Nutriboost vị cam', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Larue Smooth (500ml)', 'price' => 265000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Bia Heineken bạc', 'price' => 450000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Revive chanh', 'price' => 20000, 'loai_dich_vu' => 'nuoc'],
            ['dich_vu_name' => 'Mực khô', 'price' => 129000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Xúc xích', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Cá bò nướng', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Trái cây', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Nem nướng', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Tré trộn', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Nem chua lắc ổi', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Mì tôm trứng trộn/nước', 'price' => 30000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Mì tôm (gọi thêm)', 'price' => 8000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Cá chỉ vàng', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Chả các loại', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Trứng (gọi thêm)', 'price' => 7000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Xúc xích (gọi thêm)', 'price' => 7000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Nem chua lắc trái cây', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Bò khô', 'price' => 69000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Chả mực', 'price' => 150000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Cơm tấm', 'price' => 35000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Bánh mì ốpla', 'price' => 30000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Bò né', 'price' => 45000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Bánh mì thịt nướng/chả trứng', 'price' => 25000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Hạt điều', 'price' => 48000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Bánh mì đặc biệt', 'price' => 30000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Phomai que', 'price' => 45000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Nem chua rán', 'price' => 45000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Cá viên chiên', 'price' => 50000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Tôm viên chiên', 'price' => 50000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Bò viên chiên', 'price' => 50000, 'loai_dich_vu' => 'thucAn'],
            ['dich_vu_name' => 'Ốc nhồi', 'price' => 55000, 'loai_dich_vu' => 'thucAn'],
        ]);
    }
}
