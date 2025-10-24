<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\MovieController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\BookingController;
use App\Http\Controllers\Api\ImageProxyController;

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/send-test-email', function () {
    $recipientEmail = 'your_email@example.com'; // Replace with your email address
    Mail::to($recipientEmail)->send(new TestMail());
    return 'Test email sent to ' . $recipientEmail;
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset-password');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

// Booking routes (require authentication)
Route::middleware(['api.auth'])->group(function () {
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/seats', [BookingController::class, 'seats'])->name('booking.seats');
    Route::get('/booking/snacks', [BookingController::class, 'snacks'])->name('booking.snacks');
    Route::get('/booking/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
    Route::post('/booking/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::get('/booking/success', [BookingController::class, 'success'])->name('booking.success');
});

// Movie routes
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/movies/slug/{slug}', [MovieController::class, 'showBySlug'])->name('movies.show.slug');
Route::get('/genres', [MovieController::class, 'genres'])->name('movies.genres');

// Test routes for debugging
Route::get('/test/schedules/{movieId}', function($movieId) {
    $apiService = app(\App\Services\ApiService::class);
    
    echo "<h1>Testing Schedules API for Movie ID: $movieId</h1>";
    
    // Test API connection
    echo "<h2>1. API Base URL:</h2>";
    echo "<p>" . $apiService->getBaseUrl() . "</p>";
    
    // Test schedules endpoint
    echo "<h2>2. Testing /schedules/movie/$movieId endpoint:</h2>";
    $response = $apiService->getMovieSchedules($movieId);
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    
    // Test general schedules endpoint
    echo "<h2>3. Testing /schedules endpoint:</h2>";
    $allSchedules = $apiService->getSchedules();
    echo "<pre>" . json_encode($allSchedules, JSON_PRETTY_PRINT) . "</pre>";
    
    // Test movies endpoint
    echo "<h2>4. Testing /movies/$movieId endpoint:</h2>";
    $movieResponse = $apiService->getMovie($movieId);
    echo "<pre>" . json_encode($movieResponse, JSON_PRETTY_PRINT) . "</pre>";
    
    return "Test completed. Check the output above.";
});

// API routes for web integration
Route::prefix('api')->group(function () {
    // Image proxy for Google Drive images
    Route::get('/image-proxy', [\App\Http\Controllers\Api\ImageProxyController::class, 'proxy'])->name('image-proxy');
    
    // Review and Comment routes
    Route::post('/movies/{movieId}/reviews', function($movieId, Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        $data = $request->all();
        
        // Add user_id from session if available
        if (session('user') && isset(session('user')['id'])) {
            $data['user_id'] = session('user')['id'];
        }
        
        return response()->json($apiService->createReview($movieId, $data));
    });

    Route::post('/movies/{movieId}/comments', function($movieId, Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        $data = $request->all();
        
        // Add user_id from session if available
        if (session('user') && isset(session('user')['id'])) {
            $data['user_id'] = session('user')['id'];
        }
        
        return response()->json($apiService->createComment($movieId, $data));
    });
    Route::post('/comments/{id}/reply', function($id, Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        $data = $request->all();
        
        // Add user_id from session if available
        if (session('user') && isset(session('user')['id'])) {
            $data['user_id'] = session('user')['id'];
        }
        
        return response()->json($apiService->createCommentReply($id, $data));
    });
    Route::post('/reviews/{id}/reply', function($id, Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        $data = $request->all();
        
        // Add user_id from session if available
        if (session('user') && isset(session('user')['id'])) {
            $data['user_id'] = session('user')['id'];
        }
        
        return response()->json($apiService->createReviewReply($id, $data));
    });

    Route::post('/favorites', function(Request $request) {
    try {
        $movieId = $request->input('movie_id');
        $title = $request->input('title');
        $posterUrl = $request->input('poster_url');

        if (!$movieId) {
            return response()->json([
                'success' => false,
                'message' => 'Movie ID is required'
            ], 400);
        }

        $apiService = app(\App\Services\ApiService::class);
        
        // Prepare movie data for API call
        $movieData = [
            'title' => $title,
            'poster' => $posterUrl
        ];
        
        $result = $apiService->addToFavorites($movieId, $movieData);

        \Log::info('Add to favorites request:', [
            'movie_id' => $movieId,
            'title' => $title,
            'result' => $result
        ]);

        return response()->json($result);
    } catch (\Exception $e) {
        \Log::error('Add to favorites error:', [
            'message' => $e->getMessage(),
            'movie_id' => $request->input('movie_id')
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
});

    Route::get('/favorites', function() {
    $apiService = app(\App\Services\ApiService::class);
    $user = session('user');
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Bạn cần đăng nhập để xem danh sách yêu thích'
        ], 401);
    }
    return response()->json($apiService->getUserFavorites($user['id']));
});

    Route::delete('/favorites/{movieId}', function($movieId) {
    $apiService = app(\App\Services\ApiService::class);
    return response()->json($apiService->removeFromFavorites($movieId));
});

    // File upload route
    Route::post('/upload/file', function(Request $request) {
    $apiService = app(\App\Services\ApiService::class);
    return response()->json($apiService->uploadFile($request));
});
});

// Page routes
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/info-user', [PageController::class, 'infoUser'])->name('info-user');
Route::get('/my-tickets', [PageController::class, 'myTickets'])->name('my-tickets');
Route::get('/favorites', [PageController::class, 'favorites'])->name('favorites');

// Additional API routes
Route::prefix('api')->group(function () {
    // API routes for autocomplete
    Route::get('/movies', [MovieController::class, 'apiMovies']);

    // Upload avatar route (exclude from CSRF)
    Route::post('/upload-avatar', function(Request $request) {
        try {
            // Check if user is authenticated
            $user = session('user');
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để upload avatar'
                ], 401);
            }

            // Validate file upload
            if (!$request->hasFile('avatar')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có file được upload'
                ], 400);
            }

            $file = $request->file('avatar');
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WebP)'
                ], 400);
            }

            // Validate file size (max 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File quá lớn. Kích thước tối đa là 5MB'
                ], 400);
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store file locally
            $path = $file->storeAs('uploads/avatars', $filename, 'public');
            
            if ($path) {
                // Update user avatar in session
                $user['avatar'] = $path;
                session(['user' => $user]);
                
                // Update avatar in API (optional)
                $apiService = app(\App\Services\ApiService::class);
                $token = session('jwt_token');
                if ($token) {
                    $apiService->setToken($token);
                    $apiService->updateUserAvatar($path);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Avatar đã được cập nhật thành công',
                    'data' => [
                        'avatar_url' => url('storage/' . $path)
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể lưu file'
                ], 500);
            }
            
        } catch (\Exception $e) {
            \Log::error('Avatar upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi upload avatar: ' . $e->getMessage()
            ], 500);
        }
    })->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    // API routes for booking
    Route::get('/schedules/{scheduleId}/seats', function($scheduleId) {
    $apiService = app(\App\Services\ApiService::class);
    return response()->json($apiService->getScheduleSeats($scheduleId));
});

    Route::post('/bookings/{bookingId}/cancel', function($bookingId) {
    $apiService = app(\App\Services\ApiService::class);
    return response()->json($apiService->cancelBooking($bookingId));
});

    // Violations API routes
    Route::get('/violations', function() {
        $apiService = app(\App\Services\ApiService::class);
        return response()->json($apiService->getViolations());
    });

    Route::post('/violations', function(\Illuminate\Http\Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        return response()->json($apiService->reportViolation($request->all()));
    })->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
});

// Admin routes (require admin authentication)
Route::prefix('admin')->middleware(['api.admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/movies', [AdminController::class, 'movies'])->name('movies');
    Route::get('/movies/new', [AdminController::class, 'newMovie'])->name('movies.new');
    Route::post('/movies', [AdminController::class, 'createMovie'])->name('movies.create');
    Route::get('/movies/{id}/edit', [AdminController::class, 'editMovie'])->name('movies.edit');
    Route::put('/movies/{id}', [AdminController::class, 'updateMovie'])->name('movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'deleteMovie'])->name('movies.delete');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/test', function() {
        return view('admin.users.test');
    })->name('users.test');
        Route::get('/users/{id}', [AdminController::class, 'viewUser'])->name('users.view');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        
        // Reviews AJAX
        Route::get('/reviews/load', [AdminController::class, 'loadReviews'])->name('reviews.load');
    Route::post('/users/{id}/assign-role', [AdminController::class, 'assignUserRole'])->name('users.assign-role');
    Route::post('/users/{id}/revoke-admin', [AdminController::class, 'revokeUserAdminRole'])->name('users.revoke-admin');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/users/export', [AdminController::class, 'exportUsers'])->name('users.export');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/comments', [AdminController::class, 'comments'])->name('comments');
    Route::get('/theaters', [AdminController::class, 'theaters'])->name('theaters');
    Route::get('/theaters/create', [AdminController::class, 'createTheater'])->name('theaters.create');
    Route::post('/theaters', [AdminController::class, 'storeTheater'])->name('theaters.store');
    Route::get('/theaters/{id}', [AdminController::class, 'showTheater'])->name('theaters.show');
    Route::get('/theaters/{id}/edit', [AdminController::class, 'editTheater'])->name('theaters.edit');
    Route::put('/theaters/{id}', [AdminController::class, 'updateTheater'])->name('theaters.update');
    Route::delete('/theaters/{id}', [AdminController::class, 'destroyTheater'])->name('theaters.destroy');
    Route::get('/theaters/{theaterId}/schedules/create', [AdminController::class, 'createSchedule'])->name('schedules.create');
    Route::post('/theaters/{theaterId}/schedules', [AdminController::class, 'storeSchedule'])->name('schedules.store');
    Route::get('/schedules/{id}', [AdminController::class, 'showSchedule'])->name('schedules.show');
    Route::get('/schedules/{id}/edit', [AdminController::class, 'editSchedule'])->name('schedules.edit');
    Route::put('/schedules/{id}', [AdminController::class, 'updateSchedule'])->name('schedules.update');
    Route::delete('/schedules/{id}', [AdminController::class, 'destroySchedule'])->name('schedules.destroy');
    Route::get('/violations', [AdminController::class, 'violations'])->name('violations');
    
    // Violation management routes
    Route::put('/violations/{id}', function($id, \Illuminate\Http\Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        return response()->json($apiService->updateViolation($id, $request->all()));
    });
    Route::post('/violations/{id}/toggle-visibility', function($id, \Illuminate\Http\Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        return response()->json($apiService->toggleViolationVisibility($id, $request->all()));
    });
    
    // Comment management routes
    Route::put('/comments/{id}', function($id, \Illuminate\Http\Request $request) {
        $apiService = app(\App\Services\ApiService::class);
        return response()->json($apiService->updateComment($id, $request->all()));
    });
    Route::delete('/comments/{id}', function($id) {
        $apiService = app(\App\Services\ApiService::class);
        return response()->json($apiService->deleteComment($id));
    });
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('reports.export');
});

// Test export report endpoint (no auth required for testing)
Route::get('/test/export-report', function() {
    $adminController = app(\App\Http\Controllers\Web\AdminController::class);
    
    // Mock session user for testing
    session(['user' => [
        'id' => 1,
        'name' => 'Test Admin',
        'email' => 'admin@test.com',
        'role' => 'admin'
    ]]);
    
    return $adminController->exportReport();
});

// Public reports export route (temporary fix)
Route::get('/admin/reports/export', [AdminController::class, 'exportReport'])->name('reports.export.public');

// Test export report without permission check
Route::get('/test/export-csv', function() {
    try {
        // Create simple CSV content
        $csvContent = "BÁO CÁO THỐNG KÊ HỆ THỐNG CINEMA\n";
        $csvContent .= "Ngày xuất báo cáo: " . date('d/m/Y H:i:s') . "\n\n";
        
        // Movies Statistics
        $csvContent .= "=== THỐNG KÊ PHIM ===\n";
        $csvContent .= "Tổng số phim,10\n";
        $csvContent .= "Phim nổi bật,3\n";
        $csvContent .= "Phim mới tháng này,2\n\n";
        
        // Users Statistics
        $csvContent .= "=== THỐNG KÊ NGƯỜI DÙNG ===\n";
        $csvContent .= "Tổng người dùng,50\n";
        $csvContent .= "Đăng ký tháng này,5\n";
        $csvContent .= "Người dùng hoạt động,45\n\n";
        
        // Add BOM for UTF-8
        $bom = "\xEF\xBB\xBF";
        $csvContent = $bom . $csvContent;
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="bao_cao_thong_ke_' . date('Y-m-d') . '.csv"');
            
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test session user info
Route::get('/test/session', function() {
    $user = session('user');
    $apiService = app(\App\Services\ApiService::class);
    
    return response()->json([
        'session_user' => $user,
        'is_authenticated' => $apiService->isAuthenticated(),
        'session_id' => session()->getId(),
        'all_session' => session()->all()
    ]);
});

// Test session storage
Route::get('/test/session-storage', function() {
    // Test storing data in session
    session(['test_data' => 'Hello World']);
    session(['user' => [
        'id' => 6,
        'name' => 'tran minh tri',
        'email' => 'tritranminh484@gmail.com',
        'role' => 'admin'
    ]]);
    
    // Force save
    session()->save();
    
    return response()->json([
        'message' => 'Data stored in session',
        'session_id' => session()->getId(),
        'all_session' => session()->all()
    ]);
});

// Test session retrieval
Route::get('/test/session-retrieve', function() {
    return response()->json([
        'test_data' => session('test_data'),
        'user' => session('user'),
        'session_id' => session()->getId(),
        'all_session' => session()->all()
    ]);
});

// Test role permissions
Route::get('/test/role-check', function() {
    $user = session('user');
    
    // Test specific role permissions
    $allowedRoles = ['admin', 'review_manager', 'movie_manager', 'violation_manager'];
    $userRole = $user['role'] ?? 'no_role';
    $hasAccess = $user && in_array($userRole, $allowedRoles);
    
    // Test menu permissions
    $menuItems = [
        'dashboard' => ['admin', 'review_manager', 'movie_manager', 'violation_manager'],
        'movies' => ['admin', 'movie_manager'],
        'theaters' => ['admin', 'movie_manager'],
        'users' => ['admin'],
        'reviews' => ['admin', 'review_manager', 'violation_manager'],
        'comments' => ['admin', 'review_manager', 'violation_manager'],
        'violations' => ['admin', 'violation_manager'],
        'reports' => ['admin', 'review_manager']
    ];
    
    $menuAccess = [];
    foreach ($menuItems as $menu => $roles) {
        $menuAccess[$menu] = $user && in_array($userRole, $roles);
    }
    
    return response()->json([
        'user' => $user,
        'user_role' => $userRole,
        'allowed_roles' => $allowedRoles,
        'has_access' => $hasAccess,
        'menu_access' => $menuAccess,
        'session_id' => session()->getId()
    ]);
});
