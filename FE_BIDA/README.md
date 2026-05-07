# Hệ Thống Quản Lý Quán Bida - Frontend

**Hệ thống quản lý chuyên nghiệp phong cách SaaS dành cho câu lạc bộ Bida**

Sử dụng Vue 3 + Vite + Bootstrap 5 với giao diện UI/UX hiện đại, tinh tế.

---

##  Các tính năng mới (Vừa cập nhật)

**Hệ thống đặt bàn & Khuyến mãi thông minh**

1. **Mở bàn nhanh (Quick Open)**
   - Chọn nhanh thời gian chơi dự kiến (1h, 2h, 3h).
   - Chọn kèm **Gói Combo** ngay khi bắt đầu.
   - Hiển thị đồng hồ đếm ngược và thông báo khi sắp hết giờ (<10 phút).

2. **Dịch vụ Khuyến mãi & Giờ vàng**
   - Tự động áp dụng giảm giá trong khung giờ vàng (Happy Hour).
   - Quản lý linh hoạt các gói Combo (ví dụ: Combo 2h + 4 nước).
   - Tự động tính toán chiết khấu trên hóa đơn.

3. **Đặt bàn nhóm (Bulk Booking)**
   - Chế độ chọn nhiều bàn cùng lúc.
   - Mở hóa đơn hàng loạt cho đoàn khách đông chỉ với 1 click.

4. **Gia hạn thời gian**
   - Nút gia hạn nhanh trong chi tiết hóa đơn.
   - Cập nhật thời gian kết thúc dự kiến ngay lập tức.

---

##  Bắt đầu nhanh (5 phút)

1. **Đọc** `QUICK_START.md` trước.
2. **Cập nhật router** với các đường dẫn từ `ROUTER_SETUP.js`.
3. **Thiết lập API URL** trong file `.env.local`.
4. **Chạy lệnh**: `npm run dev`
5. **Truy cập**: `http://localhost:5174/` (Đăng nhập admin: `admin` / `123456`).

---

##  Tài liệu hướng dẫn

- **`HUONG_DAN_CAI_DAT.md`** - Hướng dẫn cài đặt chi tiết 📋
- **`API_INTEGRATION.md`** - Thông tin các cổng API 🔌
- **`ROUTER_SETUP.js`** - Cấu hình định tuyến (Router) 🛣️

---

##  Cấu trúc file chính

```
src/components/user_view/user/
├── index.vue               Danh sách bàn & Mở bàn nhanh
├── BanDetail.vue           Chi tiết hóa đơn & Gia hạn giờ
├── indexThucDon.vue        Quản lý thực đơn & Combo
```

---

## 🎨 Đặc điểm thiết kế

- ✅ Thiết kế tối giản phong cách SaaS hiện đại kết hợp hiệu ứng **Glassmorphism**.
- ✅ **Hình ảnh nền cao cấp**: Giao diện đăng nhập sử dụng hình ảnh CLB Bida sang trọng với ánh đèn Neon.
- ✅ Bo góc mềm mại, đổ bóng nhẹ nhàng.
- ✅ Nút bấm hiệu ứng Gradient sang trọng.
-  Hiệu ứng chuyển cảnh mượt mà.
-  Tương thích hoàn toàn với Mobile/Tablet/Desktop.
-  Tối ưu hóa giao diện in ấn hóa đơn.

---

##  Các tính năng chính

### Quản lý bàn
- Hiển thị lưới bàn (Grid) trực quan.
- Trạng thái bàn thời gian thực (Trống/Đang sử dụng).
- Bộ lọc loại bàn (Thường/VIP).
- Tìm kiếm bàn nhanh theo tên.

### Quản lý hóa đơn
- Tính tiền tự động theo từng phút chơi.
- Thêm/Xóa dịch vụ dễ dàng.
- Tích hợp tra cứu khách hàng VIP bằng SĐT.
- Gộp bàn linh hoạt.
- In hóa đơn chuyên nghiệp (Khổ 80mm).

---

##  Công nghệ sử dụng

- **Vue 3** - Composition API + `<script setup>`
- **Vite** - Công cụ build siêu nhanh
- **Axios** - Kết nối API
- **Bootstrap 5** - Hệ thống Grid & Components
- **CSS3** - Gradients, animations, @media print

---

##  Tùy chỉnh

### Thay đổi màu chủ đạo
Chỉnh sửa biến CSS trong component:
```css
--natural-primary: #4A5D4E; /* Màu xanh rêu sang trọng */
```

### Cập nhật mẫu hóa đơn
Chỉnh sửa trong `BanDetail.vue` phần `printReceipt()`.

---

## Hỗ trợ

Nếu gặp vấn đề trong quá trình cài đặt, vui lòng kiểm tra:
- Backend đã chạy chưa (`php artisan serve`).
- File `.env.local` đã đúng URL API chưa.
- Console trình duyệt có lỗi gì không.

**Chúc bạn quản lý hiệu quả! **

---

**Phiên bản:** 1.1 (Cập nhật tính năng Khuyến mãi & Đặt bàn nhanh)  
**Ngày cập nhật:** 21/04/2026  
**Trạng thái:**  Sẵn sàng sử dụng
