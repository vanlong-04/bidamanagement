# Hướng Dẫn Sử Dụng & Cài Đặt Hệ Thống Bida

## Tổng Quan

Hệ thống đã được nâng cấp với các tính năng chuyên sâu để quản lý quán bida một cách hiệu quả và chuyên nghiệp nhất. Dưới đây là hướng dẫn chi tiết các tính năng mới.

---

## 1️ Tính năng: Mở bàn nhanh & Combo (Mới!)

**File chính:** `src/components/user_view/user/index.vue`

### Tính năng chính:
- **Chọn thời gian dự kiến**: Khách có thể chọn chơi linh hoạt hoặc chọn trước 1h, 2h, 3h để dễ dàng quản lý thời gian.
- **Tích hợp Combo**: Cho phép chọn kèm các gói Combo (ví dụ: Combo 1h + 2 nước) ngay khi mở bàn.
- **Đồng hồ đếm ngược**: Hiển thị thời gian còn lại trực quan.
- **Cảnh báo hết giờ**: Hệ thống tự động báo đỏ và hiện Alert khi thời gian chơi còn dưới 10 phút.

---

## 2️ Tính năng: Dịch vụ Khuyến mãi & Giờ vàng

**File chính:** `backend_bida/app/Http/Controllers/HoaDonController.php`

### Cách thức hoạt động:
- **Happy Hour**: Tự động giảm giá (%) tiền giờ nếu thời gian chơi nằm trong khung giờ vàng (ví dụ 17h-19h).
- **Gói Combo**: Các gói dịch vụ gộp (Bàn + Đồ uống + Đồ ăn) với mức giá ưu đãi cố định.
- **Tính toán tự động**: Hệ thống tự trừ chiết khấu trực tiếp trên hóa đơn trước khi thanh toán.

---

## 3️ Tính năng: Đặt bàn nhóm (Multi-booking)

### Cách sử dụng:
1. Nhấn nút **"Chọn nhiều bàn"** trên thanh công cụ.
2. Tích chọn các bàn trống muốn mở cho nhóm.
3. Nhấn **"Mở bàn nhóm"**.
4. Hệ thống sẽ tự động tạo hóa đơn riêng cho từng bàn nhưng thuộc cùng một lượt phục vụ.

---

## 4️ Hướng dẫn Gia hạn giờ

**File chính:** `src/components/user_view/user/BanDetail.vue`

### Các bước thực hiện:
- Trong giao diện Chi tiết bàn, nhấn nút **"GIA HẠN GIỜ"**.
- Chọn số phút muốn thêm (ví dụ: +30p, +60p).
- Hệ thống sẽ tự động cập nhật lại thời gian kết thúc dự kiến và đồng hồ đếm ngược.

---

##  Tích hợp API mới

Để các tính năng trên hoạt động, Backend cần các Endpoint sau:

```
POST   /admin/hoa-don/create-data        → Thêm trường duration, combo_id
POST   /admin/hoa-don/extend-time        → Gia hạn thời gian
POST   /admin/hoa-don/book-multiple-tables → Mở nhiều bàn cùng lúc
GET    /admin/dich-vu/get-data           → Lấy danh sách Combo
```

---

##  Tùy chỉnh Giao diện (UI)

Hệ thống sử dụng hệ màu **Natural SaaS**:
- `--natural-primary`: #4A5D4E (Xanh rêu chủ đạo)
- `--natural-surface`: #FFFFFF (Nền trắng tinh khiết)
- `--natural-bg`: #FBFBF9 (Nền xám nhạt tự nhiên)

Bạn có thể chỉnh sửa các biến này trong phần `<style>` của các file component để thay đổi bộ nhận diện thương hiệu của quán.

---

##  Các bước cài đặt tiếp theo

1. **Backend**: Chạy `php artisan migrate` để cập nhật bảng `hoa_dons` và `promotions`.
2. **Frontend**: Chạy `npm install` để đảm bảo có đủ thư viện `bootstrap` và `axios`.
3. **Dữ liệu**: Truy cập Admin để thêm các gói Combo vào danh sách Dịch vụ (loại 'Combo').

**Cảm ơn bạn đã sử dụng hệ thống! **
