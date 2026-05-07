<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhachHang;

class KhachHangController extends Controller
{
    public function index()
    {
        $data = KhachHang::orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $data
        ]);
    }

    public function search(Request $request)
    {
        $phone = $request->query('phone');
        if (!$phone) {
            return response()->json(['data' => null]);
        }
        $khachHang = KhachHang::where('so_dien_thoai', $phone)->first();
        return response()->json([
            'data' => $khachHang
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:15|unique:khach_hangs',
            'hang_thanh_vien' => 'in:Thường,VIP'
        ]);

        $kh = KhachHang::create([
            'ten_khach_hang' => $request->ten_khach_hang,
            'so_dien_thoai' => $request->so_dien_thoai,
            'hang_thanh_vien' => $request->hang_thanh_vien ?? 'Thường'
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Thêm khách hàng thành công.',
            'data' => $kh
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'khach_hang_id' => 'required|exists:khach_hangs,khach_hang_id',
            'ten_khach_hang' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:15|unique:khach_hangs,so_dien_thoai,' . $request->khach_hang_id . ',khach_hang_id',
            'hang_thanh_vien' => 'required|in:Thường,VIP'
        ]);

        $kh = KhachHang::find($request->khach_hang_id);
        $kh->update([
            'ten_khach_hang' => $request->ten_khach_hang,
            'so_dien_thoai' => $request->so_dien_thoai,
            'hang_thanh_vien' => $request->hang_thanh_vien
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật khách hàng thành công.',
            'data' => $kh
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'khach_hang_id' => 'required|exists:khach_hangs,khach_hang_id'
        ]);

        KhachHang::find($request->khach_hang_id)->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Đã xóa khách hàng.'
        ]);
    }
}
