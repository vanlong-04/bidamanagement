<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;

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