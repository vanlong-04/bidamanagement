<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChiTietHoaDon;
use App\Models\DichVu;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Top 5 services by quantity sold TODAY
        $topServices = ChiTietHoaDon::whereDate('created_at', $today)
            ->select('dich_vu_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('dich_vu_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();

        $data = $topServices->map(function ($item) {
            $dichVu = DichVu::find($item->dich_vu_id);
            return [
                'name' => $dichVu ? $dichVu->dich_vu_name : 'Unknown',
                'count' => (int)$item->total_quantity,
            ];
        });

        // Calculate percentages for UI
        $maxCount = $data->max('count') ?: 1;
        $colors = ['#f87171', '#fbbf24', '#60a5fa', '#a8a29e', '#34d399'];
        
        $result = $data->map(function ($item, $idx) use ($maxCount, $colors) {
            return [
                'name' => $item['name'],
                'count' => $item['count'],
                'percentage' => round(($item['count'] / $maxCount) * 100),
                'color' => $colors[$idx % count($colors)]
            ];
        });

        // Daily summary stats
        $todayBills = \App\Models\HoaDon::whereDate('created_at', $today)->get();
        
        $todayRevenue = $todayBills->whereIn('status', ['đã thanh toán', 2, 'paid'])->sum('total_amount');
        $completedBills = $todayBills->whereIn('status', ['đã thanh toán', 2, 'paid'])->count();
        $totalHours = $todayBills->sum('total_hours');

        return response()->json([
            'status' => 1,
            'top_services' => $result,
            'today_revenue' => $todayRevenue,
            'completed_bills' => $completedBills,
            'total_hours' => round($totalHours, 1)
        ]);
    }
}
