# Giải Thích Luồng Xử Lý Frontend tới Backend - Nhiệm Vụ Của Hoàng (Đầy Đủ Chức Năng)

Tài liệu này giải thích chi tiết toàn bộ các nút bấm và tính năng trong module Quản lý Đặt bàn (Booking) và Thống kê Doanh thu mà **Hoàng** phụ trách.

---

## PHẦN 1: Quản Lý Khách Đặt Bàn Trước (Booking)
*File Frontend:* `FE_BIDA/src/components/admin/datban/index.vue`
*File Backend:* `DatBanController.php`

### 1. Nút "Lấy Danh Sách Đặt Bàn" (Tự động tải)
**FRONTEND:** Gọi `loadBookings()`
```javascript
loadBookings() {
    axios.get('http://127.0.0.1:8000/api/admin/dat-ban/get-data')
        .then(res => { this.bookings = res.data.data; });
}
```
**BACKEND:** Truy vấn bảng `dat_bans`, thường sẽ `orderBy('booking_time', 'DESC')` để hiện người sắp tới lên trên cùng.

### 2. Nút "THÊM ĐẶT BÀN"
**FRONTEND:** Điền Form (Tên khách, SĐT, Chọn Bàn, Giờ đến) -> Gọi `createBooking()`
```javascript
createBooking() {
    axios.post('http://127.0.0.1:8000/api/admin/dat-ban/create-data', this.create_booking)
        .then((res) => {
            if (res.data.status) {
                this.loadBookings(); // Tải lại bảng
                alert('Khách đã đặt bàn thành công!');
            }
        });
}
```
**BACKEND:** `DatBanController@store`
```php
public function store(Request $request) {
    // 1. Validate form không được trống
    $validated = $request->validate([
        'customer_name' => 'required|string',
        'booking_time' => 'required|date',
        'ban_id' => 'required|integer'
    ]);
    // 2. Insert record Đặt bàn với status 'pending' (Đang chờ)
    DatBan::create([...$validated, 'customer_phone' => $request->customer_phone, 'status' => 'pending']);
    // 3. Đổi trạng thái bàn đó thành 3 (Đã được đặt) để khóa bàn
    Ban::where('ban_id', $validated['ban_id'])->update(['status' => 3]);
    return response()->json(['status' => 1]);
}
```

### 3. Nút "Xóa / Hủy Đặt Bàn"
**FRONTEND:** Gọi `deleteBooking()` gửi lên ID cần hủy.
**BACKEND:** `DatBanController@delete`
```php
public function delete(Request $request) {
    $booking = DatBan::find($request->booking_id);
    // 1. Đổi trạng thái bàn về 1 (Trống)
    Ban::where('ban_id', $booking->ban_id)->update(['status' => 1]);
    // 2. Xóa dữ liệu đặt bàn (hoặc đổi status thành canceled)
    $booking->delete();
    return response()->json(['status' => 1]);
}
```

---

## PHẦN 2: Dashboard Báo Cáo Thống Kê Doanh Thu
*File Frontend:* `FE_BIDA/src/components/admin/doanhthu/index.vue`
*File Backend:* `DashboardController.php`

### 1. Hàm tải và phân tích dữ liệu vẽ biểu đồ
Chức năng này không bấm nút mà tự động tính toán ngay khi vào trang Thống kê.
**FRONTEND:** Gọi `getStats()`
```javascript
getStats() {
    axios.get('http://127.0.0.1:8000/api/admin/dashboard/stats')
        .then((res) => {
            // Lấy chuỗi JSON chứa doanh thu từng ngày
            const revenueData = res.data.revenue_last_7_days;
            
            // Xử lý mảng dữ liệu này bơm vào thư viện vẽ Chart.js 
            this.renderChart(revenueData);
            
            // Lấy con số tổng doanh thu tháng này đưa lên các Thẻ Thống Kê (Card)
            this.totalMonthlyRevenue = res.data.total_month;
        });
}
```

**BACKEND:** `DashboardController@getStats`
Đây là logic cốt lõi tính toán tài chính phức tạp nhất của hệ thống:
```php
public function getStats() {
    // TÍNH TOÁN 1: Tính tổng doanh thu nguyên tháng hiện tại
    // Sử dụng whereMonth của SQL kết hợp hàm sum() lấy tổng cột total_amount của các Hóa Đơn đã thanh toán (status = 1)
    $currentMonthRevenue = HoaDon::whereMonth('created_at', date('m'))
                                 ->where('status', 1)
                                 ->sum('total_amount');

    // TÍNH TOÁN 2: Tính doanh thu gom nhóm theo từng ngày (Dùng cho Biểu đồ đường - Line Chart 7 ngày)
    // - DATE(created_at): Cắt bỏ giờ phút, chỉ lấy ngày
    // - SUM(total_amount): Cộng dồn tất cả bill trong ngày đó
    // - groupBy('date'): Gộp các bill chung 1 ngày thành 1 dòng duy nhất
    $revenueLast7Days = HoaDon::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                              ->where('created_at', '>=', now()->subDays(7))
                              ->where('status', 1)
                              ->groupBy('date')
                              ->orderBy('date', 'ASC')
                              ->get();

    // Trả về Frontend 1 mảng JSON chứa tất cả kết quả tính toán trên Server
    return response()->json([
        'total_month' => $currentMonthRevenue,
        'revenue_last_7_days' => $revenueLast7Days
    ]);
}
```
