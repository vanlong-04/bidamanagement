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
}