# Hướng dẫn cài đặt nhanh

## Bước 1: Cài đặt dependencies

```bash
cd cinema-laravel
composer install
```

## Bước 2: Cấu hình môi trường

Tạo file `.env`:

```bash
cp .env.example .env
```

Cập nhật file `.env`:

```env
APP_NAME="Cinema Laravel"
APP_URL=http://localhost:8001
API_URL=http://127.0.0.1:8000/api
```

## Bước 3: Tạo application key

```bash
php artisan key:generate
```

## Bước 4: Chạy ứng dụng

```bash
php artisan serve --port=8001
```

## Bước 5: Truy cập ứng dụng

Mở trình duyệt và truy cập: http://localhost:8001

## Lưu ý quan trọng

1. **API Backend**: Đảm bảo API backend tại `cinema-api/` đang chạy trên port 8000
2. **Database**: API backend đã có database, frontend chỉ cần kết nối API
3. **CORS**: API backend cần cấu hình CORS để cho phép frontend kết nối

## Kiểm tra hoạt động

1. Trang chủ: http://localhost:8001
2. Đăng nhập: http://localhost:8001/login
3. Danh sách phim: http://localhost:8001/movies
4. Admin (nếu có quyền): http://localhost:8001/admin

## Tài khoản demo

Sử dụng tài khoản từ API backend:
- Admin: admin@example.com / password
- User: user@example.com / password
