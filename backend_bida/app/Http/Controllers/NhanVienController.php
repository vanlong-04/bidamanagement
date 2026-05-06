<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhanVien;
use Illuminate\Support\Facades\Hash;

class NhanVienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function createNhanVien(Request $request)
    {
        // Validate request
        $request->validate([
            'username' => 'required|unique:nhan_viens',
            'password' => 'required|min:6',
            'full_name' => 'required',
            'email' => 'required|email|unique:nhan_viens',
            'phone' => 'nullable',
            'hire_date' => 'required|date'
        ]);

        NhanVien::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'status' => 'active'
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Thêm nhân viên thành công',
        ]);
    }
    public function getNhanVien()
    {
        $data = NhanVien::all();
        return response()->json([
            'data' => $data
        ]);
    }
    public function updateNhanVien(Request $request)
    {
        $data = [
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'status' => $request->status
        ];

        // Chỉ hash password nếu có thay đổi
        if ($request->has('password') && !empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        NhanVien::where('nhan_vien_id', $request->nhan_vien_id)->update($data);

        return response()->json([
            'status' => 1,
            'message' => 'Cập nhật nhân viên thành công'
        ]);
    }
    public function deleteNhanVien(Request $request){
        NhanVien::where('nhan_vien_id', $request->nhan_vien_id)->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Xóa nhân viên thành công',
        ]);
    }
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $nhanVien = NhanVien::whereRaw('LOWER(username) = ?', [strtolower($username)])->first();

        if (!$nhanVien) {
            return response()->json(['status' => 0, 'message' => 'Tên đăng nhập không đúng'], 401);
        }

        // Kiểm tra password đã hash
        if (!Hash::check($password, $nhanVien->password)) {
            return response()->json(['status' => 0, 'message' => 'Mật khẩu không đúng'], 401);
        }

        // Tự động chấm công (Check-in) khi nhân viên đăng nhập
        $today = \Carbon\Carbon::today()->toDateString();
        $existing = \App\Models\ChamCong::where('nhan_vien_id', $nhanVien->nhan_vien_id)
            ->where('date', $today)
            ->whereNull('check_out_time')
            ->first();

        if (!$existing) {
            \App\Models\ChamCong::create([
                'nhan_vien_id' => $nhanVien->nhan_vien_id,
                'date' => $today,
                'check_in_time' => \Carbon\Carbon::now(),
                'status' => 'đang làm',
                'note' => 'Hệ thống tự động chấm công khi đăng nhập',
            ]);
        }

        // Nếu đúng, trả về thông tin nhân viên (bỏ trường mật khẩu)
        return response()->json([
            'status' => 1,
            'message' => 'Đăng nhập thành công và đã tự động chấm công',
            'data' => [
                'nhan_vien_id' => $nhanVien->nhan_vien_id,
                'username' => $nhanVien->username,
                'full_name' => $nhanVien->full_name,
                'email' => $nhanVien->email,
                'phone' => $nhanVien->phone,
                'status' => $nhanVien->status
            ]
        ]);
    }
}