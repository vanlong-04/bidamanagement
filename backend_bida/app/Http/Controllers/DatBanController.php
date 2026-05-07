<?php

namespace App\Http\Controllers;

use App\Models\Ban;
use App\Models\DatBan;
use Illuminate\Http\Request;

class DatBanController extends Controller
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    private function syncBanStatus(int $banId, string $bookingStatus): void
    {
        $banStatus = Ban::STATUS_TRONG;

        if (in_array($bookingStatus, [self::STATUS_PENDING, self::STATUS_CONFIRMED], true)) {
            $banStatus = Ban::STATUS_DA_DAT;
        } elseif ($bookingStatus === self::STATUS_COMPLETED) {
            $banStatus = Ban::STATUS_DANG_SU_DUNG;
        }

        Ban::where('ban_id', $banId)->update(['status' => $banStatus]);
    }

    private function makeBookingPayload(Request $request): array
    {
        return [
            'ban_id' => $request->ban_id,
            'loai_ban' => $request->loai_ban ?? Ban::LOAI_LO,
            'ten_khach_hang' => $request->ten_khach_hang,
            'so_dien_thoai' => $request->so_dien_thoai,
            'thoi_gian_dat' => $request->thoi_gian_dat,
            'so_luong_nguoi' => $request->so_luong_nguoi ?? 1,
            'ghi_chu' => $request->ghi_chu,
            'status' => self::STATUS_PENDING,
        ];
    }

    private function validateStoreRequest(Request $request): void
    {
        $request->validate([
            'ten_khach_hang' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:50',
            'thoi_gian_dat' => 'required|date',
        ]);
    }

    private function findBookingById(int $id): DatBan
    {
        return DatBan::findOrFail($id);
    }

    private function transformBooking(DatBan $booking): array
    {
        return [
            'id' => $booking->id,
            'ban_id' => $booking->ban_id,
            'loai_ban' => $booking->loai_ban,
            'ten_khach_hang' => $booking->ten_khach_hang,
            'so_dien_thoai' => $booking->so_dien_thoai,
            'thoi_gian_dat' => $booking->thoi_gian_dat,
            'so_luong_nguoi' => $booking->so_luong_nguoi,
            'ghi_chu' => $booking->ghi_chu,
            'status' => $booking->status,
            'ban' => $booking->ban ? [
                'ban_id' => $booking->ban->ban_id,
                'ban_name' => $booking->ban->ban_name,
                'loai_ban_label' => $booking->ban->loai_ban_label,
            ] : null,
        ];
    }

    public function index()
    {
        $data = DatBan::with('ban')
            ->orderBy('thoi_gian_dat', 'asc')
            ->get()
            ->map(fn($booking) => $this->transformBooking($booking));

        return response()->json([
            'status' => 1,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $this->validateStoreRequest($request);

        $booking = DatBan::create($this->makeBookingPayload($request));

        if ($booking->ban_id) {
            $this->syncBanStatus($booking->ban_id, self::STATUS_PENDING);
        }

        return $this->successResponse('Đặt bàn thành công', $this->transformBooking($booking));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:dat_bans,id',
            'status' => 'required|string',
        ]);

        $booking = $this->findBookingById($request->id);
        $booking->update(['status' => $request->status]);

        if ($booking->ban_id) {
            $this->syncBanStatus($booking->ban_id, $request->status);
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

        $booking = $this->findBookingById($request->id);
        $banId = $booking->ban_id;
        $booking->delete();

        if ($banId) {
            $this->syncBanStatus($banId, self::STATUS_CANCELLED);
        }

        return $this->successResponse('Xóa đặt bàn thành công');
    }

    private function successResponse(string $message, $data = null)
    {
        $response = [
            'status' => 1,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }
}
