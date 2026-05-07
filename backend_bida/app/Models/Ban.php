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
}
