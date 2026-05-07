# Phân Công Nhiệm Vụ Nhóm 5 Người (Code & Push Git)

Dựa theo bảng phân công công việc gốc, để đảm bảo ai cũng có **lịch sử commit code thực tế** (Fullstack FE + BE) và **không bị conflict (xung đột) file** khi push, công việc được cụ thể hóa thành các module độc lập như sau:

---

### 1. Sơn (Quản lý Bàn bida)
*Đúng theo bảng phân công: Thiết kế UI & Chức năng quản lý bàn.*
- **Nhiệm vụ:** Xây dựng giao diện và chức năng CRUD (Thêm/sửa/xóa) bàn bida, quản lý các trạng thái của bàn (Trống, đang chơi, bảo trì).
- **Backend:** 
  - File: `BanController.php`
  - Model: `Ban.php`
- **Frontend:** 
  - Thư mục: `src/components/admin/ban/`
- **Nhánh Git:** `feature/quan-ly-ban`

### 2. Long (Quản lý Dịch vụ, Setup & In Hóa đơn)
*Đúng theo bảng phân công: Quản lý dịch vụ + Xuất hóa đơn.*
*(Gánh thêm phần Setup Base Code ban đầu để anh em có sườn làm việc)*
- **Nhiệm vụ:**
  - Setup bộ khung dự án trắng ban đầu (FE Vite/Vue, BE Laravel, .env).
  - Module quản lý Dịch vụ (Đồ ăn, thức uống, cấu hình giá giờ chơi).
  - Chức năng xuất/in file PDF hóa đơn cho khách.
- **Backend:** 
  - File: `DichVuController.php`, `BidaConfigController.php`, `ChiTietHoaDonController.php`
  - Model: `DichVu.php`
- **Frontend:** 
  - Thư mục: `src/components/admin/dichvu/`
  - File in: `src/components/admin/hoaban/BillPrinter.vue`
- **Nhánh Git:** `feature/dich-vu-va-in-bill`

### 3. Quang (Tính tiền & Quản lý Khách hàng)
*Đúng theo bảng phân công: Tính tiền theo giờ/dịch vụ + Quản lý khách hàng.*
- **Nhiệm vụ:** Logic bộ đếm thời gian, tính tiền giờ chơi cộng với dịch vụ, chuyển/gộp bàn. Xây dựng module lưu trữ và quản lý thông tin khách hàng (khách quen, VIP).
- **Backend:** 
  - File: `HoaDonController.php` (logic tạo và tính tiền), `KhachHangController.php`
  - Model: `HoaDon.php`, `KhachHang.php`
- **Frontend:** 
  - Thư mục: `src/components/admin/hoaban/index.vue` (giao diện tính tiền tổng)
  - Thư mục: `src/components/admin/khachhang/`
- **Nhánh Git:** `feature/tinh-tien-khach-hang`

### 4. Hoàng (Đặt bàn & Dashboard Thống kê)
*Mở rộng từ vai trò Thiết kế DB/Phân tích để có code thực tế.*
- **Nhiệm vụ:** Xây dựng tính năng cho phép quản lý khách đặt bàn trước (Booking). Thiết kế màn hình Dashboard tổng quan, vẽ biểu đồ thống kê doanh thu theo ngày/tháng.
- **Backend:** 
  - File: `DatBanController.php`, `DashboardController.php`
  - Model: `DatBan.php`
- **Frontend:** 
  - Thư mục: `src/components/admin/datban/`
  - Thư mục: `src/components/admin/doanhthu/`
- **Nhánh Git:** `feature/dat-ban-thong-ke`

### 5. Phong (Nhân sự, Chấm công & Chat nội bộ)
*Mở rộng từ vai trò Test/Tích hợp để có code thực tế.*
- **Nhiệm vụ:** Quản lý danh sách tài khoản nhân viên, tính năng chấm công hàng ngày. Tích hợp hệ thống Chat nội bộ realtime cho nhân viên.
- **Backend:** 
  - File: `NhanVienController.php`, `ChamCongController.php`, `ChatController.php`
  - Model: `NhanVien.php`, `ChamCong.php`, `Chat.php`
- **Frontend:** 
  - Thư mục: `src/components/admin/nhanvien/`
  - Thư mục: `src/components/admin/chamcong/`
  - Component Chat (nếu có).
- **Nhánh Git:** `feature/nhan-su-chat`

---

## Hướng dẫn Push Code tránh Conflict Tuyệt Đối
1. **File độc lập:** Các file Model, Controller, và thư mục Vue Component ở trên đã được **chia hoàn toàn tách biệt**. Không ai thao tác đè lên file của người khác.
2. **File dùng chung (`routes/api.php` và `FE_BIDA/src/router/index.js`):**
   - Mỗi người tự viết code route của mình vào **một khu vực riêng** (thêm comment tên mình phía trên, ví dụ: `// Routes của Sơn`).
   - Khi merge PR nếu báo conflict ở file route, chỉ cần chọn **Accept Both Changes** (Giữ cả 2 đoạn code) là xong.
3. **Quy trình chuẩn:**
   - Cập nhật code mới nhất: `git checkout develop` -> `git pull origin develop`
   - Chuyển sang nhánh mình làm: `git checkout feature/<nhánh-của-bạn>`
   - Bỏ file code của mình vào -> Commit -> Push lên nhánh.
