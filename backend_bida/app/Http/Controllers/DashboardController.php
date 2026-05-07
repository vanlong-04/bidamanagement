<?php

namespace App\Http\Controllers;

use App\Models\Ban;
use App\Models\DatBan;
use App\Models\HoaDon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        $today = now()->toDateString();

        $totalBookings = DatBan::count();
        $confirmedBookings = DatBan::where('status', 'confirmed')->count();
        $pendingBookings = DatBan::where('status', 'pending')->count();
        $cancelledBookings = DatBan::where('status', 'cancelled')->count();

        $todayRevenue = HoaDon::whereDate('created_at', $today)
            ->whereIn('status', ['paid', 2, 'đã thanh toán'])
            ->sum('total_amount');

        $activeTables = Ban::where('status', Ban::STATUS_DANG_SU_DUNG)->count();
        $reservedTables = Ban::where('status', Ban::STATUS_DA_DAT)->count();

        return response()->json([
            'status' => 1,
            'data' => [
                'total_bookings' => $totalBookings,
                'confirmed_bookings' => $confirmedBookings,
                'pending_bookings' => $pendingBookings,
                'cancelled_bookings' => $cancelledBookings,
                'today_revenue' => $todayRevenue,
                'active_tables' => $activeTables,
                'reserved_tables' => $reservedTables,
            ]
        ]);
    }
}
