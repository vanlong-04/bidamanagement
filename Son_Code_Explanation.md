# Giải Thích Luồng Xử Lý Frontend tới Backend - Nhiệm Vụ Của Sơn (Đầy Đủ Chức Năng)

Tài liệu này giải thích chi tiết luồng hoạt động giao tiếp giữa Frontend (Vue.js) và Backend (Laravel) dành riêng cho chức năng Quản lý Bàn bida mà **Sơn** phụ trách. Tất cả các nút bấm và tính năng trên giao diện đều được giải thích luồng đi dưới đây.

---

## 1. Nút "Cập Nhật Cấu Hình Giá Giờ"
*File Frontend:* `FE_BIDA/src/components/admin/ban/index.vue`
*File Backend:* `BidaConfigController.php`

### 1.1 Lấy giá giờ hiện tại khi mở trang
**FRONTEND:** Gọi hàm `getHourlyRates()`
```javascript
getHourlyRates() {
    axios.get('http://127.0.0.1:8000/api/admin/bida-config/get-hourly-rates')
        .then(res => {
            // Lấy giá bida lỗ, bida phăng, lỗ VIP, phăng VIP gán vào biến hourlyRates
            if (res.data) this.hourlyRates = res.data;
        })
}
```
**BACKEND:** Truy vấn bảng `bida_configs` lấy cấu hình giá đang áp dụng.

### 1.2 Lưu giá giờ mới (Nút "Lưu cấu hình giá mới")
**FRONTEND:** Gọi hàm `saveHourlyRates()`
```javascript
saveHourlyRates() {
    this.savingRates = true; // Hiện loading
    // Đẩy cục dữ liệu hourlyRates chứa giá tiền lên Backend
    axios.post('http://127.0.0.1:8000/api/admin/bida-config/set-hourly-rates', this.hourlyRates)
        .then(res => {
            this.rateMessage = res.data.message || 'Đã lưu giá giờ!';
            alert(this.rateMessage);
        })
}
```
**BACKEND:** Update lại giá trị trong Database bằng hàm `setHourlyRates()` trong `BidaConfigController`.

---

## 2. Lấy Danh Sách Bàn (Hiển thị Bảng/Sơ đồ)
**FRONTEND:** Gọi hàm `getTables()` tự động khi trang load.
```javascript
getTables() {
    axios.get('http://127.0.0.1:8000/api/admin/ban/get-data')
        .then((res) => {
            // Lấy toàn bộ mảng các bàn hiện có gán vào biến 'tables'
            this.tables = res.data.data;
        })
}
```
**BACKEND:** `BanController@getBan`
```php
public function getBan(Request $request) {
    // Truy vấn SQL: Lấy danh sách bàn kèm thông tin khách đang đặt (nếu có)
    $data = Ban::with('activeBooking')->get();
    return response()->json(['data' => $data]);
}
```

---

## 3. Thêm Bàn Mới (Nút "THÊM BÀN MỚI")
**FRONTEND:** Mở Modal, điền form rồi bấm nút "Thêm" -> Gọi `createTable()`
```javascript
createTable() {
    // Đẩy thông tin (Tên bàn, Loại bàn, Trạng thái) lên Backend
    axios.post('http://127.0.0.1:8000/api/admin/ban/create-data', this.create_table)
        .then((res) => {
            if (res.data.status) {
                this.getTables(); // Reload lại bảng
                this.create_table = { ban_name: '', loai_ban: 1, status: 1 } // Xóa trắng form
                alert(res.data.message);
            }
        });
}
```
**BACKEND:** `BanController@createBan`
```php
public function createBan(Request $request) {
    // 1. Kiểm tra (Validate) dữ liệu không bị trống và tên bàn không bị trùng lặp
    $payload = $request->validate([
        'ban_name' => 'required|string|max:50|unique:bans,ban_name',
        'loai_ban' => 'required|integer|in:1,2,3,4',
        'status' => 'nullable|integer|in:1,2',
    ]);
    // 2. Insert dữ liệu vào DB
    Ban::create($payload);
    return response()->json(['message' => 'Bàn đã được tạo thành công', 'status' => 1]);
}
```

---

## 4. Sửa Thông Tin Bàn (Nút Cây Bút)
**FRONTEND:** Chọn bàn cần sửa để điền vào Modal, sau đó bấm "Cập nhật" -> Gọi `updateTable()`
```javascript
updateTable() {
    // Đẩy dữ liệu mới (kèm ID bàn) lên Backend
    axios.post('http://127.0.0.1:8000/api/admin/ban/update-data', this.update_table)
        .then((res) => {
            if (res.data.status) {
                this.getTables(); // Reload lại bảng ngay
                alert(res.data.message);
            }
        });
}
```
**BACKEND:** `BanController@updateBan`
```php
public function updateBan(Request $request) {
    // 1. Validate kiểm tra bàn ID có tồn tại không
    $payload = $request->validate([
        'ban_id' => 'required|integer|exists:bans,ban_id',
        'ban_name' => 'required|string|max:50|unique:bans,ban_name,' . $request->ban_id . ',ban_id',
    ]);
    // 2. Tìm ID bàn đó trong DB và update các thuộc tính
    Ban::where('ban_id', $payload['ban_id'])->update([
        'ban_name' => $payload['ban_name'],
        'loai_ban' => $request->loai_ban,
        'status' => $request->status,
    ]);
    return response()->json(['message' => 'Bàn đã được cập nhật thành công', 'status' => 1]);
}
```

---

## 5. Xóa Bàn (Nút Thùng Rác)
**FRONTEND:** Xác nhận xóa trong Modal -> Gọi `deleteTable()`
```javascript
deleteTable() {
    // Đẩy đối tượng chứa `ban_id` lên Backend để yêu cầu xóa
    axios.post('http://127.0.0.1:8000/api/admin/ban/delete-data', this.delete_table)
        .then((res) => {
            if (res.data.status) {
                this.getTables(); // Cập nhật lại UI sau khi xóa thành công
                alert(res.data.message);
            }
        });
}
```
**BACKEND:** `BanController@deleteBan`
```php
public function deleteBan(Request $request) {
    // Tìm bàn theo ban_id và tiến hành Delete khỏi Database
    Ban::where('ban_id', $request->ban_id)->delete();
    return response()->json(['message' => 'Bàn đã được xóa thành công', 'status' => 1]);
}
```
