<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChiTietHoaDon;

class ChiTietHoaDonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){

    }
    public function createChiTietHoaDon(Request $request){
        $chiTietHoaDon = ChiTietHoaDon::create([
            'dich_vu_id' => $request->dich_vu_id,
            'hoa_don_id' => $request->hoa_don_id,
            'quantity' => $request->so_luong,
            'price' => $request->price,
            'total' => $request->price * $request->so_luong
        ]);
        return response()->json([
            "message" => "Chi tiết hóa đơn đã được tạo thành công",
            "data" => $chiTietHoaDon
        ]);
    }
    public function updateChiTietHoaDon(Request $request){
        $chiTietHoaDon = ChiTietHoaDon::where('chi_tiet_hoa_don_id', $request->chi_tiet_hoa_don_id)->update([
            'quantity' => $request->so_luong,
            'total' => $request->price * $request->so_luong
        ]);
        return response()->json([
            "message" => "Chi tiết hóa đơn đã được cập nhật thành công",
            "data" => $chiTietHoaDon
        ]);
    }
    public function deleteChiTietHoaDon(Request $request){
        ChiTietHoaDon::where('chi_tiet_hoa_don_id', $request->chi_tiet_hoa_don_id)->delete();
        return response()->json([
            "message" => "Chi tiết hóa đơn đã được xóa thành công",
        ]);
    }
    public function getChiTietHoaDon(Request $request){
        $data = ChiTietHoaDon::with(['dichVu', 'hoaDon'])
            ->where('hoa_don_id', $request->hoa_don_id)
            ->get()
            ->map(function($item) {
                return [
                    'chi_tiet_hoa_don_id' => $item->chi_tiet_hoa_don_id,
                    'dich_vu_id' => $item->dich_vu_id,
                    'dich_vu_name' => $item->dichVu->dich_vu_name,
                    'loai_dich_vu' => $item->dichVu->loai_dich_vu,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                    'start_time' => $item->created_at,
                    'end_time' => $item->hoaDon->end_time
                ];
            });
        return response()->json([
            "data" => $data
        ]);
    }
}
