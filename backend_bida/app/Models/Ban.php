<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ban extends Model
{
    use HasFactory;

    public const LOAI_LO = 1;
    public const LOAI_PHANG = 2;
    public const LOAI_LO_VIP = 3;
    public const LOAI_PHANG_VIP = 4;

    public const STATUS_TRONG = 1;
    public const STATUS_DANG_SU_DUNG = 2;
    public const STATUS_DA_DAT = 3;

    protected $table = 'bans';
    protected $primaryKey = 'ban_id';
    public $timestamps = true;

    protected $appends = [
        'loai_ban_label',
        'hourly_rate',
        'name', 
    ];

    protected $fillable = [
        'ban_name',
        'loai_ban',
        'status',
    ];

    public function bookings()
    {
        return $this->hasMany(DatBan::class, 'ban_id');
    }

    public function activeBooking()
    {
        return $this->hasOne(DatBan::class, 'ban_id')
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('thoi_gian_dat', 'asc');
    }
}
