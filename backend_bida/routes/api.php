<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BanController;
use App\Http\Controllers\BidaConfigController;
// Cấu hình giá giờ bàn
Route::get('/admin/bida-config/get-hourly-rates', [BidaConfigController::class, 'getHourlyRates']);
Route::post('/admin/bida-config/set-hourly-rates', [BidaConfigController::class, 'setHourlyRates']);
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\HoaDonController;
use App\Models\Ban;
use App\Http\Controllers\ChiTietHoaDonController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatBanController;

Route::get("/admin/dashboard/stats", [DashboardController::class, "getStats"]);

Route::get("/admin/dat-ban/get-data", [DatBanController::class, "index"]);
Route::post("/admin/dat-ban/create-data", [DatBanController::class, "store"]);
Route::post("/admin/dat-ban/update-status", [DatBanController::class, "updateStatus"]);
Route::post("/admin/dat-ban/delete-data", [DatBanController::class, "destroy"]);
Route::post("/admin/ban/create-data", [BanController::class, "createBan"]);
Route::get("/admin/ban/get-data", [BanController::class, "getBan"]);
Route::post("/admin/ban/update-data", [BanController::class, "updateBan"]);
Route::post("/admin/ban/delete-data", [BanController::class, "deleteBan"]);

Route::post("/admin/dich-vu/create-data", [DichVuController::class, "createDichVu"]);
Route::get("/admin/dich-vu/get-data", [DichVuController::class, "getDichVu"]);
Route::post("/admin/dich-vu/update-data", [DichVuController::class, "updateDichVu"]);
Route::post("/admin/dich-vu/delete-data", [DichVuController::class, "deleteDichVu"]);

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

Route::post("/admin/chi-tiet-hoa-don/create-data", [ChiTietHoaDonController::class, "createChiTietHoaDon"]);
Route::get("/admin/chi-tiet-hoa-don/get-data", [ChiTietHoaDonController::class, "getChiTietHoaDon"]);
Route::post("/admin/chi-tiet-hoa-don/update-data", [ChiTietHoaDonController::class, "updateChiTietHoaDon"]);
Route::post("/admin/chi-tiet-hoa-don/delete-data", [ChiTietHoaDonController::class, "deleteChiTietHoaDon"]);

Route::post("/admin/nhan-vien/create-data", [NhanVienController::class, "createNhanVien"]);
Route::get("/admin/nhan-vien/get-data", [NhanVienController::class, "getNhanVien"]);
Route::post("/admin/nhan-vien/update-data", [NhanVienController::class, "updateNhanVien"]);
Route::post("/admin/nhan-vien/delete-data", [NhanVienController::class, "deleteNhanVien"]);
Route::post('/login', [NhanVienController::class, 'login']);

use App\Http\Controllers\ChamCongController;
Route::post('/admin/cham-cong/check-in', [ChamCongController::class, 'checkIn']);
Route::post('/admin/cham-cong/check-out', [ChamCongController::class, 'checkOut']);
Route::get('/admin/cham-cong/get-data', [ChamCongController::class, 'getChamCong']);
Route::post('/admin/cham-cong/delete-data', [ChamCongController::class, 'deleteChamCong']);

use App\Http\Controllers\KhachHangController;
Route::get('/admin/khach-hang/get-data', [KhachHangController::class, 'index']);
Route::get('/admin/khach-hang/search', [KhachHangController::class, 'search']);
Route::post('/admin/khach-hang/create-data', [KhachHangController::class, 'store']);
Route::post('/admin/khach-hang/update-data', [KhachHangController::class, 'update']);
use App\Http\Controllers\ChatController;
Route::post('/admin/chat/send', [ChatController::class, 'sendMessage']);
Route::get('/admin/chat/get-messages', [ChatController::class, 'getMessages']);

