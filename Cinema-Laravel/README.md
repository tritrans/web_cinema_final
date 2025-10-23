# Cinema Laravel Frontend

Đây là frontend Laravel cho hệ thống quản lý rạp chiếu phim, được chuyển đổi từ Next.js sang Laravel Blade.

## Tính năng

- 🎬 **Trang chủ**: Hiển thị phim nổi bật và phim mới nhất
- 🔍 **Tìm kiếm phim**: Tìm kiếm phim theo tên, mô tả
- 📱 **Responsive**: Giao diện thân thiện trên mọi thiết bị
- 👤 **Đăng nhập/Đăng ký**: Hệ thống xác thực người dùng
- ⭐ **Đánh giá & Bình luận**: Người dùng có thể đánh giá và bình luận phim
- 🎭 **Thể loại phim**: Xem phim theo thể loại
- 👨‍💼 **Admin Dashboard**: Quản lý người dùng, đánh giá, bình luận
- 🔐 **Phân quyền**: Hệ thống phân quyền cho admin, manager, user

## Cài đặt

### 1. Cài đặt dependencies

```bash
composer install
```

### 2. Cấu hình môi trường

Tạo file `.env` từ `.env.example`:

```bash
cp .env.example .env
```

Cập nhật các cấu hình sau trong file `.env`:

```env
APP_NAME="Cinema Laravel"
APP_URL=http://localhost:8001

# API Configuration - Điều chỉnh URL API backend
API_URL=http://127.0.0.1:8000/api
```

### 3. Tạo application key

```bash
php artisan key:generate
```

### 4. Chạy migrations (nếu cần)

```bash
php artisan migrate
```

### 5. Chạy ứng dụng

```bash
php artisan serve --port=8001
```

Truy cập ứng dụng tại: http://localhost:8001

## Cấu trúc dự án

```
cinema-laravel/
├── app/
│   ├── Http/Controllers/Web/     # Controllers cho web
│   │   ├── HomeController.php    # Trang chủ
│   │   ├── AuthController.php    # Xác thực
│   │   ├── MovieController.php   # Quản lý phim
│   │   └── AdminController.php   # Admin dashboard
│   └── Services/
│       └── ApiService.php        # Service kết nối API
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php         # Layout chính
│   ├── home.blade.php            # Trang chủ
│   ├── auth/                     # Trang đăng nhập/đăng ký
│   ├── movies/                   # Trang phim
│   └── admin/                    # Trang admin
└── routes/
    └── web.php                   # Routes web
```

## API Backend

Frontend này kết nối với API Laravel backend có sẵn tại `cinema-api/`. Đảm bảo:

1. API backend đang chạy tại `http://127.0.0.1:8000`
2. Cấu hình `API_URL` trong `.env` đúng với URL API backend
3. API backend có đầy đủ dữ liệu phim, người dùng, đánh giá

## Tính năng chính

### Trang chủ
- Hiển thị phim nổi bật (hero section)
- Danh sách phim nổi bật
- Danh sách phim mới nhất
- Thống kê và tính năng nổi bật

### Quản lý phim
- Danh sách tất cả phim
- Tìm kiếm phim
- Chi tiết phim với đánh giá và bình luận
- Xem phim theo thể loại

### Hệ thống xác thực
- Đăng nhập/Đăng ký
- Quản lý session với JWT token
- Phân quyền người dùng

### Admin Dashboard
- Thống kê tổng quan
- Quản lý người dùng
- Quản lý đánh giá
- Quản lý bình luận
- Phân quyền theo role

## Công nghệ sử dụng

- **Laravel 12**: Framework PHP
- **Blade**: Template engine
- **Tailwind CSS**: CSS framework
- **Font Awesome**: Icons
- **JavaScript**: Tương tác frontend
- **HTTP Client**: Kết nối API

## Phân quyền

Hệ thống hỗ trợ các role sau:

- **super_admin**: Quyền cao nhất
- **admin**: Quản trị viên
- **review_manager**: Quản lý đánh giá
- **movie_manager**: Quản lý phim
- **violation_manager**: Quản lý vi phạm
- **user**: Người dùng thường

## Troubleshooting

### Lỗi kết nối API
- Kiểm tra API backend có đang chạy không
- Kiểm tra cấu hình `API_URL` trong `.env`
- Kiểm tra CORS settings trong API backend

### Lỗi 500
- Kiểm tra logs trong `storage/logs/laravel.log`
- Đảm bảo đã chạy `php artisan key:generate`
- Kiểm tra quyền ghi file trong thư mục `storage/`

### Lỗi 404
- Kiểm tra routes trong `routes/web.php`
- Đảm bảo đã chạy `php artisan route:clear`

## Đóng góp

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Mở Pull Request

## License

Dự án này được phân phối dưới MIT License.