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
    public function getHoaDon(Request $request)
    {
        $data = HoaDon::with(['ban', 'chiTietHoaDons.dichVu'])->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $data,
        ]);
    }
    public function updateHoaDon(Request $request)
    {
        $payload = $request->validate([
            'hoa_don_id' => 'required|integer|exists:hoa_dons,hoa_don_id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'total_hours' => 'nullable|numeric|min:0',
            'status' => 'nullable|string',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $hoaDon = HoaDon::with(['ban', 'chiTietHoaDons'])->find($payload['hoa_don_id']);

        $startTime = isset($payload['start_time'])
            ? new \DateTime($payload['start_time'])
            : ($hoaDon->start_time ? new \DateTime($hoaDon->start_time) : null);
        $endTime = isset($payload['end_time'])
            ? new \DateTime($payload['end_time'])
            : new \DateTime();

        $totalHours = (float) ($payload['total_hours'] ?? 0);
        $charge = 0;

        if ($startTime) {
            $diffInSeconds = max(0, $endTime->getTimestamp() - $startTime->getTimestamp());
            $billableMinutes = (int) ceil($diffInSeconds / 60);
            $totalHours = round($diffInSeconds / 3600, 2);
            $hourlyRate = (int) ($hoaDon->ban->hourly_rate ?? config('bida.hourly_rates.thuong', 50000));
            $charge = (int) (round((($billableMinutes / 60) * $hourlyRate) / 100) * 100);
        }

        $serviceTotal = (float) $hoaDon->chiTietHoaDons->sum(function ($item) {
            return $item->total ?? ((float) $item->price * (int) $item->quantity);
        });

        // Happy Hour Logic
        $discountAmount = 0;
        $happyHour = Promotion::where('type', 'happy_hour')->where('is_active', true)->first();
        if ($happyHour) {
            $config = $happyHour->config;
            $now = Carbon::now();
            $startTimeHH = Carbon::createFromFormat('H:i', $config['start_time']);
            $endTimeHH = Carbon::createFromFormat('H:i', $config['end_time']);
            
            if ($now->between($startTimeHH, $endTimeHH)) {
                $discountPercent = $config['discount_percent'] ?? 0;
                $discountAmount = ($charge * $discountPercent) / 100;
            }
        }

        $totalAmount = $serviceTotal + $charge - $discountAmount;

        $hoaDon->update([
            'start_time' => $startTime ? $startTime->format('Y-m-d H:i:s') : null,
            'end_time' => $endTime->format('Y-m-d H:i:s'),
            'total_hours' => $totalHours,
            'charge' => $charge,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => $payload['status'] ?? $hoaDon->status,
            'payment_method' => $payload['payment_method'] ?? $hoaDon->payment_method,
        ]);

        return response()->json([
            'message' => 'Hóa đơn đã được cập nhật thành công',
            'status' => 1,
        ]);
    }
    public function deleteHoaDon(Request $request)
    {
        HoaDon::where('hoa_don_id', $request->hoa_don_id)->delete();
        return response()->json([
            'message' => 'Hóa đơn đã được xóa thành công',
            'status' => 1,
        ]);
    }
    public function updateStatus(Request $request)
    {
        $data = Ban::find($request->ban_id);

        if (!$data) {
            return response()->json(['message' => 'Không tìm thấy bàn'], 404);
        }

        $data->status = $request->status;
        $data->save();

        return response()->json(['message' => 'Cập nhật trạng thái bàn thành công']);
    }
    public function updateEndTime(Request $request)
    {
        $data = HoaDon::find($request->hoa_don_id);
        if ($data) {
            $data->end_time = $request->end_time;
            $data->save();
            return response()->json(['message' => 'Cập nhật end_time thành công']);
        }
        return response()->json(['message' => 'Không tìm thấy hóa đơn'], 404);
    }
}