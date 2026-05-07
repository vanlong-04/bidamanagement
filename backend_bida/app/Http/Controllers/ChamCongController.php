<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChamCong;
use Carbon\Carbon;

class ChamCongController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,nhan_vien_id',
        ]);

        $today = Carbon::today()->toDateString();
        $nhan_vien_id = $request->nhan_vien_id;

        // Check if already checked in today without checking out
        $existing = ChamCong::where('nhan_vien_id', $nhan_vien_id)
            ->where('date', $today)
            ->whereNull('check_out_time')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Nhân viên đã check-in và chưa check-out.',
                'status' => 0
            ], 400);
        }

        $chamCong = ChamCong::create([
            'nhan_vien_id' => $nhan_vien_id,
            'date' => $today,
            'check_in_time' => Carbon::now(),
            'status' => 'đang làm',
            'note' => $request->note ?? null,
        ]);

        return response()->json([
            'message' => 'Check-in thành công',
            'data' => $chamCong,
            'status' => 1
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,nhan_vien_id',
        ]);

        $today = Carbon::today()->toDateString();
        $nhan_vien_id = $request->nhan_vien_id;

        $chamCong = ChamCong::where('nhan_vien_id', $nhan_vien_id)
            ->where('date', $today)
            ->whereNull('check_out_time')
            ->first();

        if (!$chamCong) {
            return response()->json([
                'message' => 'Không tìm thấy ca làm việc đang mở của nhân viên hôm nay.',
                'status' => 0
            ], 404);
        }

        $chamCong->update([
            'check_out_time' => Carbon::now(),
            'status' => 'đã về',
        ]);

        return response()->json([
            'message' => 'Check-out thành công',
            'data' => $chamCong,
            'status' => 1
        ]);
    }

    public function getChamCong(Request $request)
    {
        $query = ChamCong::with('nhanVien');

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('nhan_vien_id')) {
            $query->where('nhan_vien_id', $request->nhan_vien_id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $data,
            'status' => 1
        ]);
    }

    public function deleteChamCong(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cham_congs,id',
        ]);

        ChamCong::where('id', $request->id)->delete();

        return response()->json([
            'message' => 'Xóa lịch sử chấm công thành công',
            'status' => 1
        ]);
    }
}
