<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dich_vus';
    protected $primaryKey = 'dich_vu_id';
    public $timestamps = true;

    protected $fillable = [
        'dich_vu_name',
        'loai_dich_vu',
        'price',
        'description',
        'image_path',
        'is_active',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        return url('image/' . ltrim($this->image_path, '/'));
    }
}
