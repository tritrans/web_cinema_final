@echo off
echo Starting Cinema Laravel Frontend...
echo.

REM Check if .env exists
if not exist .env (
    echo Creating .env file...
    copy .env.example .env
    echo.
    echo Please update API_URL in .env file to point to your backend API
    echo Default: API_URL=http://127.0.0.1:8000/api
    echo.
    pause
)

REM Generate app key if not exists
php artisan key:generate

echo.
echo Starting Laravel development server on port 8001...
echo Frontend will be available at: http://localhost:8001
echo.
echo Make sure your API backend is running on port 8000
echo.

php artisan serve --port=8001
