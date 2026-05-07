<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ban;

class BanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    /**
     * Get the ban data.
     */
    public function getBan(Request $request)
    {
        $data = Ban::with('activeBooking')->get();
        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createBan(Request $request)
    {
        $payload = $request->validate([
            'ban_name' => 'required|string|max:50|unique:bans,ban_name',
            'loai_ban' => 'required|integer|in:1,2,3,4',
            'status' => 'nullable|integer|in:1,2',
        ]);

        Ban::create([
            'ban_name' => $payload['ban_name'],
            'loai_ban' => $payload['loai_ban'],
            'status' => $payload['status'] ?? 1,
        ]);
        return response()->json([
            'message' => 'Bàn đã được tạo thành công',
            'status' => 1,
        ]);
    }
    public function updateBan(Request $request)
    {
        $payload = $request->validate([
            'ban_id' => 'required|integer|exists:bans,ban_id',
            'ban_name' => 'required|string|max:50|unique:bans,ban_name,' . $request->ban_id . ',ban_id',
            'loai_ban' => 'required|integer|in:1,2,3,4',
            'status' => 'required|integer|in:1,2',
        ]);

        Ban::where('ban_id', $payload['ban_id'])->update([
            'ban_name' => $payload['ban_name'],
            'loai_ban' => $payload['loai_ban'],
            'status' => $payload['status'],
        ]);
        return response()->json([
            'message' => 'Bàn đã được cập nhật thành công',
            'status' => 1,
        ]);
    }
    public function deleteBan(Request $request)
    {
        Ban::where('ban_id', $request->ban_id)->delete();
        return response()->json([
            'message' => 'Bàn đã được xóa thành công',
            'status' => 1,
        ]);
    }
}
