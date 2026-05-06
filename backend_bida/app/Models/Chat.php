<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
    ];

    public function sender()
    {
        return $this->belongsTo(NhanVien::class, 'sender_id', 'nhan_vien_id');
    }

    public function receiver()
    {
        return $this->belongsTo(NhanVien::class, 'receiver_id', 'nhan_vien_id');
    }
}
