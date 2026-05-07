<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    use HasFactory;

    protected $table = 'nhan_viens';
    protected $primaryKey = 'nhan_vien_id';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'email',
        'phone',
        'hire_date',
        'status',
    ];

    public function chamCongs()
    {
        return $this->hasMany(ChamCong::class, 'nhan_vien_id', 'nhan_vien_id');
    }
}
