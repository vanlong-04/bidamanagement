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
}