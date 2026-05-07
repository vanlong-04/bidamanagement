<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatBan extends Model
{
    protected $table = 'dat_bans';
    protected $fillable = [
        'ban_id',
        'loai_ban',
        'ten_khach_hang',
        'so_dien_thoai',
        'thoi_gian_dat',
        'so_luong_nguoi',
        'ghi_chu',
        'status'
    ];

    public function ban()
    {
        return $this->belongsTo(Ban::class, 'ban_id');
    }
}
