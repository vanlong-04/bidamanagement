<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotions';
    protected $primaryKey = 'promotion_id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'type',
        'config',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];
}
