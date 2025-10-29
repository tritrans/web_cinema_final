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
                // Absolute URL using current request host to avoid wrong APP_URL
                $avatarUrl = request()->getSchemeAndHttpHost() . '/storage/' . $path;
                \Log::info('WEB upload-avatar start', [
                    'user_id' => session('user.id'),
                    'has_token' => (bool) session('jwt_token'),
                    'avatar_url' => $avatarUrl,
                    'path' => $path,
                ]);

            // Persist to API database FIRST
            $apiService = app(\App\Services\ApiService::class);
            $token = session('jwt_token') ?? request()->cookie('jwt_token');
            if ($token) {
                    $apiService->setToken($token);
                    // Try dedicated avatar endpoint
                    $apiResponse = $apiService->updateUserAvatar($avatarUrl);
                    \Log::info('WEB upload-avatar call /users/avatar', [
                        'response_success' => is_array($apiResponse) ? ($apiResponse['success'] ?? null) : null,
                    ]);
                    // Fallback: try generic update if needed
                    if (!(is_array($apiResponse) && ($apiResponse['success'] ?? false))) {
                        $sessionUser = session('user');
                        if (isset($sessionUser['id'])) {
                            \Log::warning('WEB upload-avatar fallback to PUT /users/{id}', ['user_id' => $sessionUser['id']]);
                            $apiResponse = $apiService->updateUser($sessionUser['id'], ['avatar' => $avatarUrl]);
                            \Log::info('WEB upload-avatar fallback response', [
                                'response_success' => is_array($apiResponse) ? ($apiResponse['success'] ?? null) : null,
                            ]);
                        }
                    }

                    if (!(is_array($apiResponse) && ($apiResponse['success'] ?? false))) {
                        // Rollback stored file to avoid dangling file if desired (optional)
                        // Storage::disk('public')->delete($path);
                        \Log::error('WEB upload-avatar failed to persist avatar to API DB');
                        return response()->json([
                            'success' => false,
                            'message' => 'Không thể cập nhật avatar vào CSDL. Vui lòng thử lại.',
                        ], 500);
                    }

                    // Sync session from API response if available; otherwise set avatar manually
                    if (isset($apiResponse['data'])) {
                        session(['user' => $apiResponse['data']]);
                    } else {
                        $user = session('user') ?: [];
                        $user['avatar'] = $avatarUrl;
                        session(['user' => $user]);
                    }
                    session()->save();
                } else {
                    // No token: still update session so reload keeps new avatar
                    $user = session('user') ?: [];
                    $user['avatar'] = $avatarUrl;
                    session(['user' => $user]);
                    session()->save();
                    \Log::warning('WEB upload-avatar no jwt_token; updated session only');
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Avatar đã được cập nhật thành công',
                    'data' => [
                        'avatar_url' => $avatarUrl
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
        Route::get('/users/{id}', [AdminController::class, 'viewUser'])->name('users.view');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        
        // Reviews AJAX
        Route::get('/reviews/load', [AdminController::class, 'loadReviews'])->name('reviews.load');
    Route::post('/users/{id}/assign-role', [AdminController::class, 'assignUserRole'])->name('users.assign-role');
    Route::post('/users/{id}/revoke-admin', [AdminController::class, 'revokeUserAdminRole'])->name('users.revoke-admin');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
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

// Public reports export route
Route::get('/admin/reports/export', [AdminController::class, 'exportReport'])->name('reports.export.public');

// Public export routes
Route::get('/users/export', [AdminController::class, 'exportUsers'])->name('users.export');
