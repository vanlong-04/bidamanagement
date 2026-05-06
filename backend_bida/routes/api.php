<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\KhachHangController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Quang's billing routes
Route::post("/admin/hoa-don/create-data", [HoaDonController::class, "createHoaDon"]);
Route::get("/admin/hoa-don/get-data", [HoaDonController::class, "getHoaDon"]);
Route::post("/admin/hoa-don/update-data", [HoaDonController::class, "updateHoaDon"]);
Route::post("/admin/hoa-don/delete-data", [HoaDonController::class, "deleteHoaDon"]);
Route::post("/admin/hoa-don/extend-time", [HoaDonController::class, "extendTime"]);
Route::post("/admin/hoa-don/book-multiple-tables", [HoaDonController::class, "bookMultipleTables"]);
Route::post("/admin/hoa-don/update-status", [HoaDonController::class, "updateStatus"]);
Route::post("/admin/hoa-don/update-end-time", [HoaDonController::class, "updateEndTime"]);
Route::get("/admin/hoa-don/get-bill-by-ban-id", [HoaDonController::class, "getBillByBanId"]);
Route::post("/admin/hoa-don/gop-ban", [HoaDonController::class, "gopBan"]);

// Quang's customer routes
Route::get('/admin/khach-hang/get-data', [KhachHangController::class, 'index']);
Route::get('/admin/khach-hang/search', [KhachHangController::class, 'search']);
Route::post('/admin/khach-hang/create-data', [KhachHangController::class, 'store']);
Route::post('/admin/khach-hang/update-data', [KhachHangController::class, 'update']);
Route::post('/admin/khach-hang/delete-data', [KhachHangController::class, 'destroy']);

