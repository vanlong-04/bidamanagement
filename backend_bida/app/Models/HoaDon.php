<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;

    protected $table = 'hoa_dons';
    protected $primaryKey = 'hoa_don_id';
    public $timestamps = true;

    protected $fillable = [
        'ban_id',
        'nhan_vien_id',
        'start_time',
        'end_time',
        'total_hours',
        'charge',
        'total_amount',
        'status',
        'payment_method',
        'expected_end_time',
        'discount_amount',
        'promotion_id',
    ];

    public function chiTietHoaDons()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'hoa_don_id', 'hoa_don_id');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id', 'nhan_vien_id');
    }

    public function ban()
    {
        return $this->belongsTo(Ban::class, 'ban_id', 'ban_id');
    }
}
