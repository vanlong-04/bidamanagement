<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\NhanVien;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $payload = $request->validate([
            'sender_id' => 'required|integer',
            'receiver_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        Chat::create($payload);

        return response()->json([
            'status' => 1,
            'message' => 'Tin nhắn đã gửi',
        ]);
    }

    public function getMessages(Request $request)
    {
        // Simple logic: get all messages between two users or broadcast
        // For simplicity, we just return the latest 50 messages
        $messages = Chat::with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->take(50)
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $messages,
        ]);
    }
}
