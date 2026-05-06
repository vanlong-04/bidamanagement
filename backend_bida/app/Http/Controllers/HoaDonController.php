<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDon;
use App\Models\Ban;
use App\Models\ChiTietHoaDon;
use App\Models\DichVu;
use App\Models\Promotion;
use Carbon\Carbon;

class HoaDonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    /**
     * Show the form for creating a new resource.
     */
    public function createHoaDon(Request $request)
    {
        $defaultNhanVienId = 1; 
        
        $expectedEndTime = null;
        if ($request->duration && $request->duration > 0) {
            $expectedEndTime = Carbon::now()->addMinutes($request->duration);
        }

        $hoaDon = HoaDon::create([
            'ban_id' => $request->ban_id,
            'nhan_vien_id' => $request->nhan_vien_id ?? $defaultNhanVienId,
            'start_time' => $request->start_time ?? Carbon::now(),
            'end_time' => $request->end_time,
            'total_hours' => $request->total_hours ?? 0,
            'total_amount' => $request->total_amount ?? 0,
            'status' => $request->status ?? 'chưa thanh toán',
            'payment_method' => $request->payment_method ?? 'cash',
            'expected_end_time' => $expectedEndTime ?? $request->expected_end_time,
        ]);

        // Nếu có chọn Combo khi mở bàn
        if ($request->combo_id) {
            $dichVu = DichVu::find($request->combo_id);
            if ($dichVu) {
                ChiTietHoaDon::create([
                    'hoa_don_id' => $hoaDon->hoa_don_id,
                    'dich_vu_id' => $dichVu->dich_vu_id,
                    'quantity' => 1,
                    'price' => $dichVu->price,
                    'total' => $dichVu->price,
                ]);
                // Cập nhật lại tổng tiền HĐ (sơ bộ)
                $hoaDon->total_amount = $dichVu->price;
                $hoaDon->save();
            }
        }

        return response()->json([
            'message' => 'Hóa đơn đã được tạo thành công',
            'data' => $hoaDon,
        ]);
    }
}