<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChamCong extends Model
{
    use HasFactory;

    protected $table = 'cham_congs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nhan_vien_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'note',
    ];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id', 'nhan_vien_id');
    }
}
