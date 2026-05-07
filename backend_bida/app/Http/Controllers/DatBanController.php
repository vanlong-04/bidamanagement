<?php

namespace App\Http\Controllers;

use App\Models\Ban;
use App\Models\DatBan;
use Illuminate\Http\Request;

class DatBanController extends Controller
{
    public function index()
    {
        $data = DatBan::with('ban')
            ->orderBy('thoi_gian_dat', 'asc')
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:50',
            'thoi_gian_dat' => 'required|date',
        ]);

        $booking = DatBan::create([
            'ban_id' => $request->ban_id,
            'loai_ban' => $request->loai_ban ?? Ban::LOAI_LO,
            'ten_khach_hang' => $request->ten_khach_hang,
            'so_dien_thoai' => $request->so_dien_thoai,
            'thoi_gian_dat' => $request->thoi_gian_dat,
            'so_luong_nguoi' => $request->so_luong_nguoi ?? 1,
            'ghi_chu' => $request->ghi_chu,
            'status' => 'pending',
        ]);

        if ($booking->ban_id) {
            Ban::where('ban_id', $booking->ban_id)
                ->update(['status' => Ban::STATUS_DA_DAT]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Đặt bàn thành công',
            'data' => $booking,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:dat_bans,id',
            'status' => 'required|string',
        ]);

        $booking = DatBan::findOrFail($request->id);
        $booking->update(['status' => $request->status]);

        if ($booking->ban_id) {
            $newBanStatus = Ban::STATUS_TRONG;
            if ($request->status === 'confirmed' || $request->status === 'pending') {
                $newBanStatus = Ban::STATUS_DA_DAT;
            } elseif ($request->status === 'completed') {
                $newBanStatus = Ban::STATUS_DANG_SU_DUNG;
            }

            Ban::where('ban_id', $booking->ban_id)
                ->update(['status' => $newBanStatus]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật trạng thái đặt bàn thành công',
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:dat_bans,id',
        ]);

        $booking = DatBan::findOrFail($request->id);
        $banId = $booking->ban_id;
        $booking->delete();

        if ($banId) {
            Ban::where('ban_id', $banId)
                ->update(['status' => Ban::STATUS_TRONG]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Xóa đặt bàn thành công',
        ]);
    }
}
