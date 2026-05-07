# Giải Thích Luồng Xử Lý Frontend tới Backend - Nhiệm Vụ Của Long (Đầy Đủ Chức Năng)

Tài liệu này giải thích chi tiết toàn bộ các nút bấm và tính năng dành riêng cho các chức năng mà **Long** phụ trách (Dịch vụ, In/Xuất Hóa đơn).

---

## PHẦN 1: Quản Lý Dịch Vụ (Đồ ăn, Thức uống)
*File Frontend:* `FE_BIDA/src/components/admin/dichvu/index.vue`
*File Backend:* `DichVuController.php`

### 1. Nút "Lấy Danh Sách Dịch Vụ" (Tự động chạy khi mở trang)
**FRONTEND:** Gọi hàm `getServices()`
```javascript
getServices() {
    axios.get('http://127.0.0.1:8000/api/admin/dich-vu/get-data')
        .then((res) => {
            // Nhận kết quả JSON, gán vào mảng 'services' để Vue render ra bảng HTML
            this.services = res.data.data || []
            this.filterServices() // Chạy thêm hàm lọc dữ liệu (theo từ khóa/thể loại)
        })
}
```
**BACKEND:** `DichVuController@getDichVu`
```php
public function getDichVu(Request $request){
    // Dùng Eloquent Model lấy toàn bộ dữ liệu (SELECT * FROM dich_vus)
    $data = DichVu::all();
    return response()->json(['data' => $data]);
}
```

### 2. Nút "THÊM DỊCH VỤ"
**FRONTEND:** Điền thông tin (Tên, Loại, Giá, Ảnh) -> Bấm Thêm -> Gọi `createService()`
```javascript
createService() {
    // Vì có upload file ảnh, bắt buộc khởi tạo FormData()
    const formData = new FormData()
    formData.append('dich_vu_name', this.create_service.dich_vu_name)
    // ... đính kèm giá, mô tả...
    if (this.createImageFile) {
        formData.append('image', this.createImageFile) // Đính file ảnh thật
    }
    // Header phải là 'multipart/form-data'
    axios.post('http://127.0.0.1:8000/api/admin/dich-vu/create-data', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }).then((res) => {
        if (res.data.status) {
            this.getServices() // Load lại bảng
            alert(res.data.message)
        }
    })
}
```
**BACKEND:** `DichVuController@createDichVu`
```php
public function createDichVu(Request $request){
    // 1. Validate dữ liệu & ảnh
    $validated = $request->validate([
        'dich_vu_name' => ['required', 'string', 'max:100'],
        'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
    ]);
    // 2. Lưu ảnh vật lý vào Server
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('dichvu', 'public');
    }
    // 3. Tạo record DB (chỉ lưu đường dẫn ảnh)
    DichVu::create([
        'dich_vu_name' => $validated['dich_vu_name'],
        'image_path' => $imagePath,
    ]);
    return response()->json(['status' => 1, 'message' => 'Tạo dịch vụ thành công!']);
}
```

### 3. Nút "Sửa Dịch Vụ" (Nút Cây Bút)
**FRONTEND:** Điền form sửa -> Bấm Cập nhật -> Gọi `updateService()`
```javascript
updateService() {
    const formData = new FormData()
    formData.append('dich_vu_id', this.update_service.dich_vu_id) // ID dịch vụ cần sửa
    // ... đính kèm các thuộc tính khác...
    if (this.updateImageFile) {
        formData.append('image', this.updateImageFile) // Ảnh mới nếu có
    }
    axios.post('http://127.0.0.1:8000/api/admin/dich-vu/update-data', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => { this.getServices(); });
}
```
**BACKEND:** `DichVuController@updateDichVu`
```php
public function updateDichVu(Request $request){
    $dichVu = DichVu::where('dich_vu_id', $request->dich_vu_id)->first();
    // Logic cập nhật: Nếu có upload ảnh mới thì xóa ảnh cũ trên ổ cứng đi và thay bằng ảnh mới
    if ($request->hasFile('image')) {
        $newPath = $request->file('image')->store('dichvu', 'public');
        if ($dichVu->image_path) {
            Storage::disk('public')->delete($dichVu->image_path); // Xóa ảnh cũ
        }
        $updatePayload['image_path'] = $newPath;
    }
    $dichVu->update($updatePayload);
    return response()->json(['status' => 1]);
}
```

### 4. Nút "Xóa Dịch Vụ" (Nút Thùng Rác)
**FRONTEND:** Xác nhận xóa -> Gọi `deleteService()`
```javascript
deleteService() {
    axios.post('http://127.0.0.1:8000/api/admin/dich-vu/delete-data', this.delete_service)
        .then(res => { this.getServices(); });
}
```
**BACKEND:** `DichVuController@deleteDichVu`
```php
public function deleteDichVu(Request $request){
    $dichVu = DichVu::where('dich_vu_id', $request->dich_vu_id)->first();
    // Dọn rác: Xóa file ảnh vật lý khỏi Server trước khi xóa dữ liệu trong DB
    if ($dichVu->image_path) Storage::disk('public')->delete($dichVu->image_path);
    $dichVu->delete();
    return response()->json(['status' => 1]);
}
```

---

## PHẦN 2: In & Xuất Hóa Đơn PDF (Bill Printer)
*File Frontend:* `FE_BIDA/src/components/admin/hoaban/BillPrinter.vue`

### 1. Chọn Bàn để xem Hóa Đơn (Select box)
**FRONTEND:** Gọi API `loadBillData()` khi thay đổi thẻ `<select>`
```javascript
async loadBillData() {
    // Truyền số ID bàn lên URL thông qua tham số GET
    const res = await axios.get(`http://127.0.0.1:8000/api/admin/hoa-don/get-bill-by-ban-id?ban_id=${this.selectedTableId}`)
    // Gán dữ liệu trả về vào biến billData để hiển thị ra HTML dạng hóa đơn
    this.billData = res.data.data
}
```
*(Backend do API của Quang phụ trách, trả về dữ liệu Hóa đơn tổng cộng)*

### 2. Nút "In"
Sử dụng hàm mặc định của Trình duyệt Web (Browser) để bật hộp thoại in giấy:
```javascript
printBill() {
    window.print(); // Gọi tính năng In (Print) của Chrome/Cốc Cốc
}
```
(CSS `@media print` đã được cấu hình trong FE để ẩn đi các thanh menu khi in, chỉ in hóa đơn ra máy POS).

### 3. Nút "Xuất PDF"
Sử dụng xử lý nội bộ phía máy khách (Client-side) thay vì Server-side để giảm tải Backend.
```javascript
async exportPDF() {
    // 1. Dynamic import thư viện `html2pdf.js` (Chỉ load khi user thật sự bấm tải PDF)
    const html2pdf = (await import('html2pdf.js')).default
    
    // 2. Chụp khung giao diện thẻ chứa Hóa đơn HTML
    const element = this.$refs.billContent
    
    // 3. Render ra hình ảnh chất lượng cao và Convert qua PDF chuẩn khổ giấy A4
    await html2pdf().set({
        margin: 10,
        filename: `hoa-don-${this.selectedTableId}.pdf`,
        html2canvas: { scale: 2 }, // Tăng độ nét 2x
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    }).from(element).save() // Tiến hành tải file về máy khách hàng
}
```
