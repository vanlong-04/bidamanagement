# Giải Thích Luồng Xử Lý Frontend tới Backend - Nhiệm Vụ Của Quang (Đầy Đủ Chức Năng)

Tài liệu này giải thích chi tiết toàn bộ các nút bấm và tính năng trong module Hóa đơn, Tính tiền và Quản lý Khách hàng mà **Quang** phụ trách.

---

## PHẦN 1: Quản Lý Khách Hàng
*File Frontend:* `FE_BIDA/src/components/admin/khachhang/index.vue`
*File Backend:* `KhachHangController.php`

### 1. Nút "Lấy danh sách khách hàng" (Tự động chạy)
**FRONTEND:** Gọi `getCustomers()`
```javascript
getCustomers() {
    axios.get('http://127.0.0.1:8000/api/admin/khach-hang/get-data')
        .then(res => { this.customers = res.data.data; });
}
```
**BACKEND:** Dùng `KhachHang::all()` lấy toàn bộ danh sách, trả về chuẩn JSON.

### 2. Nút "Thêm khách hàng"
**FRONTEND:** Gọi `createCustomer()`
```javascript
createCustomer() {
    // Đẩy thông tin (Tên, SĐT, Điểm tích lũy, Loại VIP)
    axios.post('http://127.0.0.1:8000/api/admin/khach-hang/create-data', this.create_customer)
        .then(res => { this.getCustomers(); }); // Load lại bảng
}
```
**BACKEND:** `KhachHangController@createKhachHang`
```php
public function createKhachHang(Request $request) {
    // Validate tránh trùng số điện thoại
    $payload = $request->validate([
        'name' => 'required|string',
        'phone' => 'required|string|unique:khach_hangs,phone',
    ]);
    KhachHang::create([...$payload, 'is_vip' => $request->is_vip]);
    return response()->json(['status' => 1]);
}
```

### 3. Nút "Sửa thông tin khách hàng"
**FRONTEND:** Gọi `updateCustomer()` gửi thông tin cập nhật (có kèm `khach_hang_id`).
**BACKEND:** Tìm khách hàng theo ID và gọi hàm `update()`.

---

## PHẦN 2: Tính Tiền Hóa Đơn Mở Bàn
*File Frontend:* `FE_BIDA/src/components/admin/hoaban/index.vue`
*File Backend:* `HoaDonController.php` và `ChiTietHoaDonController.php`

### 1. Lấy danh sách bàn đang chơi & Bàn trống
**FRONTEND:** Gọi 2 hàm riêng biệt để hiển thị 2 cột (Bàn trống để mở / Bàn đang chơi để thêm đồ).
```javascript
loadAvailableTables() {
    // Gọi API lấy toàn bộ bàn, dùng filter() JS để lọc lấy bàn status = 1 (Trống)
}
loadActiveTables() {
    // Gọi API hóa đơn lấy những hóa đơn có status = 0 (Chưa thanh toán)
    axios.get('http://127.0.0.1:8000/api/admin/hoa-don/get-active-tables').then(...)
}
```

### 2. Nút "Bấm để mở bàn"
**FRONTEND:** Chọn 1 bàn trống -> Bấm mở -> Gọi `openTable(ban_id)`
```javascript
openTable(ban_id) {
    axios.post('http://127.0.0.1:8000/api/admin/hoa-don/create-data', { ban_id: ban_id })
        .then(res => { this.loadActiveTables(); this.loadAvailableTables(); })
}
```
**BACKEND:** `HoaDonController@createHoaDon`
```php
public function createHoaDon(Request $request) {
    // 1. Tạo Hóa Đơn mới với status = 0, lưu giờ hiện tại làm start_time
    HoaDon::create(['ban_id' => $request->ban_id, 'start_time' => now(), 'status' => 0]);
    // 2. Cập nhật trạng thái Bàn đó thành 2 (Đang sử dụng)
    Ban::where('ban_id', $request->ban_id)->update(['status' => 2]);
    return response()->json(['status' => 1]);
}
```

### 3. Nút "Thêm dịch vụ" (Khách gọi nước uống, đồ ăn)
**FRONTEND:** Bấm nút Thêm trong màn hình chi tiết hóa đơn đang chơi -> Gọi `addService()`
```javascript
addService() {
    // Gửi ID Hóa đơn, ID Dịch vụ, Số lượng và Đơn giá gốc
    axios.post('http://127.0.0.1:8000/api/admin/chi-tiet-hoa-don/create-data', payload)
        .then(...) // Cập nhật lại hóa đơn hiển thị
}
```
**BACKEND:** Tạo record lưu vào bảng chi tiết hóa đơn, đồng thời tính toán `total = quantity * price`.

### 4. Nút Sửa số lượng / Xóa dịch vụ
Tương tự nút Thêm, Frontend sẽ gửi POST request gọi `update-data` (sửa quantity) hoặc `delete-data` (xóa dòng) tới `ChiTietHoaDonController`.

### 5. Khung Nhập Số Điện Thoại (Tìm Khách VIP để giảm giá)
**FRONTEND:** Khi user gõ SĐT vào ô tìm kiếm -> Gọi `searchCustomer()`
```javascript
searchCustomer() {
    axios.post('http://127.0.0.1:8000/api/admin/khach-hang/search-by-phone', { phone: this.checkoutPhone })
        .then(res => {
            if(res.data.status) {
                this.vipDiscountPercent = res.data.discount_percent; // Lấy % giảm giá áp dụng vào tổng tiền
            }
        });
}
```
**BACKEND:** Quét DB tìm `where('phone', $request->phone)->first()`. Trả về % giảm giá.

### 6. Nút "THANH TOÁN" (Chốt sổ Hóa Đơn)
**FRONTEND:** Bấm Thanh toán -> Gọi `checkoutTable()`
```javascript
checkoutTable() {
    axios.post('http://127.0.0.1:8000/api/admin/hoa-don/checkout', {
        hoa_don_id: this.currentBill.hoa_don_id
    }).then(res => { alert('Đã thanh toán!'); })
}
```
**BACKEND:** `HoaDonController@checkout`
```php
public function checkout(Request $request) {
    $hoaDon = HoaDon::find($request->hoa_don_id);
    
    // 1. Chốt giờ ra (end_time)
    $hoaDon->end_time = now();
    
    // 2. Tính số giờ chơi bằng phép trừ thời gian (diffInMinutes)
    $totalHours = ceil($hoaDon->start_time->diffInMinutes($hoaDon->end_time) / 60);
    
    // 3. Cộng tổng tiền: Tiền giờ + Tiền chi tiết dịch vụ
    $tienGio = $totalHours * $hoaDon->ban->hourly_rate;
    $tienDichVu = $hoaDon->chiTietHoaDons->sum('total');
    
    // 4. Lưu tổng tiền, đánh dấu Hóa Đơn đã thanh toán (status = 1)
    $hoaDon->total_amount = $tienGio + $tienDichVu;
    $hoaDon->status = 1; 
    $hoaDon->save();

    // 5. Giải phóng bàn: Đổi trạng thái bàn thành 1 (Trống)
    $hoaDon->ban->update(['status' => 1]);

    return response()->json(['status' => 1]);
}
```
