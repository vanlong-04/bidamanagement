Hướng dẫn chạy Project với XAMPP
1. Cài đặt phần mềm cần thiết
XAMPP: https://www.apachefriends.org/
Composer: https://getcomposer.org/
Node.js & npm: https://nodejs.org/
2. Giải nén project
Đặt thư mục backend vào:
C:\xampp\htdocs\backend_bida
3. Tạo database MySQL
Mở XAMPP Control Panel → Start MySQL
Vào http://localhost/phpmyadmin
Tạo database mới, ví dụ: bida_db
4. Cấu hình file .env
Copy .env.example thành .env trong backend_bida
Sửa các dòng sau:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bida_db
DB_USERNAME=root
DB_PASSWORD=
5. Cài đặt các package PHP
Mở CMD/Terminal tại thư mục backend_bida:
composer install

6. Tạo key và migrate database
php artisan key:generate
php artisan migrate

7. Chạy backend
php artisan serve
→ Backend chạy ở http://127.0.0.1:8000

8. Chạy frontend
Vào thư mục FE_BIDA:
Copy .env.example thành .env.local
npm install
npm run dev
→ Frontend chạy ở http://localhost:5173

9. Truy cập hệ thống
Frontend: http://localhost:5173
Backend API: http://127.0.0.1:8000
10. Lưu ý
Nếu lỗi CORS: kiểm tra file config/cors.php trong backend.
Nếu lỗi 404: kiểm tra lại đường dẫn API và cấu hình .env.local của FE.
Nếu dùng Apache thay vì php artisan serve, phải trỏ đúng vào thư mục public của Laravel.