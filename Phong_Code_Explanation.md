# Giải Thích Luồng Xử Lý Frontend tới Backend - Nhiệm Vụ Của Phong (Đầy Đủ Chức Năng)

Tài liệu này giải thích chi tiết toàn bộ các nút bấm và tính năng trong module Quản lý Nhân sự & Chấm công mà **Phong** phụ trách.

---

## PHẦN 1: Quản Lý Nhân Sự (CRUD Nhân Viên)
*File Frontend:* `FE_BIDA/src/components/admin/nhanvien/index.vue`
*File Backend:* `NhanVienController.php`

### 1. Nút "Lấy Danh Sách Nhân Viên" (Tự chạy)
**FRONTEND:** Gọi `getStaff()` -> **BACKEND:** `NhanVien::all()` trả về mảng danh sách đưa lên bảng giao diện.

### 2. Nút "THÊM NHÂN VIÊN"
**FRONTEND:** Điền thông tin nhân viên -> Bấm Thêm -> Gọi `createStaff()`
```javascript
createStaff() {
    axios.post('http://127.0.0.1:8000/api/admin/nhan-vien/create-data', this.create_staff)
        .then((res) => {
            if (res.data.status) {
                this.getStaff(); // Load lại bảng
                alert('Đã thêm nhân viên mới!');
            }
        });
}
```
**BACKEND:** `NhanVienController@create`
```php
public function create(Request $request) {
    // 1. Kiểm tra SĐT không được trùng lặp trong bảng nhan_viens
    $validated = $request->validate([
        'name' => 'required|string',
        'role' => 'required|string',
        'phone' => 'required|string|unique:nhan_viens,phone',
    ]);
    // 2. Insert vào Database
    NhanVien::create($request->all());
    return response()->json(['status' => 1]);
}
```

### 3. Nút "Sửa Nhân Viên" & "Xóa Nhân Viên"
Tương tự thêm nhân viên, gọi API tới endpoint `update-data` (sử dụng lệnh `update()` trong Laravel) hoặc `delete-data` (sử dụng lệnh `delete()`).

---

## PHẦN 2: Module Chấm Công (Check-in / Check-out)
*File Frontend:* `FE_BIDA/src/components/admin/chamcong/index.vue`
*File Backend:* `ChamCongController.php`

### 1. Nút "Lịch sử chấm công"
**FRONTEND:** Gọi `getAttendanceHistory()` -> **BACKEND:** Lấy toàn bộ record bảng `cham_congs` join với bảng `nhan_viens` để lấy tên.

### 2. Nút "CHECK-IN" (Bắt đầu ca làm)
**FRONTEND:** Nhân viên quét mã hoặc nhập ID bấm Check-in -> Gọi `checkIn()`
```javascript
checkIn() {
    axios.post('http://127.0.0.1:8000/api/admin/cham-cong/check-in', { nhan_vien_id: this.current_staff_id })
        .then((res) => {
            alert(res.data.message); // Hiển thị thông báo (Thành công hoặc Lỗi đã check-in rồi)
            this.getAttendanceHistory(); // Refresh bảng
        });
}
```
**BACKEND:** `ChamCongController@checkIn`
```php
public function checkIn(Request $request) {
    $today = now()->format('Y-m-d'); // Lấy ngày hôm nay của máy chủ
    
    // 1. Quét Database xem nhân viên này hôm nay đã Check-in lần nào chưa
    $exists = ChamCong::where('nhan_vien_id', $request->nhan_vien_id)
                      ->whereDate('date', $today)
                      ->first();
                      
    // Nếu có dữ liệu => Đã check-in => Báo lỗi chặn lại
    if ($exists) {
        return response()->json(['status' => 0, 'message' => 'Hôm nay bạn đã check-in rồi!']);
    }

    // 2. Nếu chưa, tạo Record chấm công mới ghi nhận giờ phút giây hiện tại (now)
    ChamCong::create([
        'nhan_vien_id' => $request->nhan_vien_id,
        'date' => $today,
        'check_in_time' => now(), // Giờ bấm máy
        'status' => 'present' // Có mặt
    ]);

    return response()->json(['status' => 1, 'message' => 'Check-in thành công.']);
}
```

### 3. Nút "CHECK-OUT" (Kết thúc ca làm)
**FRONTEND:** Nhân viên bấm Check-out khi về -> Gọi `checkOut()`
```javascript
checkOut() {
    axios.post('http://127.0.0.1:8000/api/admin/cham-cong/check-out', { nhan_vien_id: this.current_staff_id })
        .then(res => { alert(res.data.message); });
}
```
**BACKEND:** `ChamCongController@checkOut`
```php
public function checkOut(Request $request) {
    $today = now()->format('Y-m-d');
    
    // 1. Tìm lại chính xác dòng Check-in đầu ngày của nhân viên này
    $chamCong = ChamCong::where('nhan_vien_id', $request->nhan_vien_id)
                        ->whereDate('date', $today)
                        ->first();

    if ($chamCong) {
        // 2. Update cột check_out_time bằng giờ phút hiện tại
        $chamCong->check_out_time = now();
        $chamCong->save(); // Lưu xuống Database
        
        return response()->json(['status' => 1, 'message' => 'Check-out thành công. Hẹn gặp lại!']);
    }

    // Nếu bấm Check-out mà sáng chưa Check-in => Báo lỗi
    return response()->json(['status' => 0, 'message' => 'Bạn chưa Check-in hôm nay.']);
}
```
