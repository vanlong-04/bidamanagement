<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DatBan;
use Carbon\Carbon;

class DatBanController extends Controller
{
    public function index()
    {
        $data = DatBan::with('ban')->orderBy('thoi_gian_dat', 'asc')->get();
        return response()->json([
            'status' => 1,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_khach_hang' => 'required',
            'so_dien_thoai' => 'required',
            'thoi_gian_dat' => 'required',
        ]);

        $datBan = DatBan::create([
            'ban_id' => $request->ban_id,
            'loai_ban' => $request->loai_ban ?? 1,
            'ten_khach_hang' => $request->ten_khach_hang,
            'so_dien_thoai' => $request->so_dien_thoai,
            'thoi_gian_dat' => $request->thoi_gian_dat,
            'so_luong_nguoi' => $request->so_luong_nguoi ?? 1,
            'ghi_chu' => $request->ghi_chu,
            'status' => 'pending'
        ]);

        // Cập nhật trạng thái bàn sang "Đã đặt" (3)
        if ($request->ban_id) {
            \App\Models\Ban::where('ban_id', $request->ban_id)->update(['status' => 3]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Đặt bàn thành công',
            'data' => $datBan
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:dat_bans,id',
            'status' => 'required'
        ]);

        $datBan = DatBan::find($request->id);
        $datBan->update(['status' => $request->status]);

        // Đồng bộ trạng thái bàn
        if ($datBan->ban_id) {
            if ($request->status === 'completed') {
                \App\Models\Ban::where('ban_id', $datBan->ban_id)->update(['status' => 2]); // Đang sử dụng
            } else if ($request->status === 'cancelled') {
                \App\Models\Ban::where('ban_id', $datBan->ban_id)->update(['status' => 1]); // Trống
            } else if ($request->status === 'confirmed' || $request->status === 'pending') {
                \App\Models\Ban::where('ban_id', $datBan->ban_id)->update(['status' => 3]); // Đã đặt
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:dat_bans,id',
        ]);

        DatBan::where('id', $request->id)->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Xóa lịch đặt bàn thành công'
        ]);
    }
}
