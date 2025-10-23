<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Check if user has admin access
     */
    private function checkAdminAccess()
    {
        $user = session('user');
        
        \Log::info('Admin access check:', [
            'user_exists' => !is_null($user),
            'user_role' => $user['role'] ?? 'no_role',
            'session_id' => session()->getId(),
            'user_data' => $user
        ]);
        
        if (!$user || !in_array($user['role'], ['admin', 'review_manager', 'movie_manager', 'violation_manager'])) {
            \Log::warning('Admin access denied:', [
                'user' => $user,
                'redirect_to' => 'home'
            ]);
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        \Log::info('Admin access granted for role:', ['role' => $user['role']]);
        return null;
    }

    /**
     * Admin Dashboard
     */
    public function index()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;

        try {
            $user = session('user');
            
            // Get dashboard data from API
            $dashboardData = [
                'movies' => [],
                'users' => [],
                'reviews' => [],
                'comments' => [],
                'theaters' => [],
                'violations' => [],
                'reports' => [],
            ];

            // Fetch data based on user role
            if (in_array($user['role'], ['admin', 'movie_manager'])) {
                $moviesResponse = $this->apiService->getMovies();
                
                if ($moviesResponse['success']) {
                    $allMovies = $moviesResponse['data']['data'] ?? [];
                    
                    $dashboardData['movies']['total'] = count($allMovies);
                    $dashboardData['movies']['featured'] = collect($allMovies)->filter(fn($movie) => $movie['featured'])->count();
                    
                    // Lấy 5 phim có release_date mới nhất
                    $dashboardData['movies']['recent'] = collect($allMovies)
                        ->filter(function($movie) {
                            // Chỉ lấy phim có release_date
                            return !empty($movie['release_date']);
                        })
                        ->sortByDesc('release_date') // Sắp xếp theo release_date mới nhất
                        ->take(5)
                        ->map(function($movie) {
                            return [
                                'id' => $movie['id'],
                                'title' => $movie['title'],
                                'genre' => $movie['genres'][0]['name'] ?? 'N/A',
                                'rating' => $movie['rating'] ?? 0, // Fixed: use 'rating' instead of 'vote_average'
                                'release_date' => $movie['release_date'],
                                'created_at' => $movie['created_at'],
                                'poster_url' => $movie['poster_url'] ?? $movie['poster'] ?? null
                            ];
                        })
                        ->values() // Reset array keys
                        ->toArray();
                }
            }

            if ($user['role'] === 'admin') {
                $usersResponse = $this->apiService->getUsers();
                if ($usersResponse['success']) {
                    $allUsers = $usersResponse['data'] ?? [];
                    $dashboardData['users']['total'] = count($allUsers);
                    $dashboardData['users']['admins'] = collect($allUsers)->filter(fn($u) => $u['role'] === 'admin')->count();
                    $dashboardData['users']['regular_users'] = $dashboardData['users']['total'] - $dashboardData['users']['admins'];
                    
                    // Lọc chỉ những user đăng ký trong ngày hôm nay (24 giờ qua)
                    $today = now('Asia/Ho_Chi_Minh');
                    $dashboardData['users']['recent'] = collect($allUsers)
                        ->filter(function($u) use ($today) {
                            $createdAt = \Carbon\Carbon::parse($u['created_at']);
                            return $createdAt->isToday() || $createdAt->diffInHours($today) <= 24;
                        })
                        ->sortByDesc('created_at')
                        ->take(10) // Tăng lên 10 để có đủ user mới trong ngày
                        ->map(function($u) {
                            return [
                                'id' => $u['id'],
                                'name' => $u['name'],
                                'email' => $u['email'],
                                'role' => $u['role'],
                                'avatar' => $u['avatar'],
                                'created_at' => $u['created_at']
                            ];
                        })
                        ->toArray();
                    
                    // Đếm số user mới trong ngày
                    $dashboardData['users']['today'] = count($dashboardData['users']['recent']);
                }
            }

            if (in_array($user['role'], ['admin', 'review_manager', 'violation_manager'])) {
                $reviewsResponse = $this->apiService->getReviews();
                if ($reviewsResponse['success']) {
                    $allReviews = $reviewsResponse['data'] ?? [];
                    $dashboardData['reviews']['total'] = count($allReviews);
                    $dashboardData['reviews']['average_rating'] = collect($allReviews)->avg('rating') ?? 0;
                }

                $commentsResponse = $this->apiService->getComments();
                if ($commentsResponse['success']) {
                    $allComments = $commentsResponse['data'] ?? [];
                    $dashboardData['comments']['total'] = count($allComments);
                    $dashboardData['comments']['today'] = collect($allComments)->filter(fn($c) => \Carbon\Carbon::parse($c['created_at'])->isToday())->count();
                }
            }
            
            return view('admin.dashboard', ['user' => $user, 'stats' => $dashboardData]);

        } catch (\Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Không thể tải trang quản trị');
        }
    }

    /**
     * Movies Management
     */
    public function movies()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $moviesResponse = $this->apiService->getMovies();
            $movies = $moviesResponse['success'] ? ($moviesResponse['data']['data'] ?? []) : [];

            // Data is now loaded directly from API, no need to merge session data

            return view('admin.movies.index', compact('movies', 'user'));

        } catch (\Exception $e) {
            Log::error('Admin movies error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Không thể tải danh sách phim');
        }
    }

    public function editMovie($id)
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Get movie details from API
            $movieResponse = $this->apiService->getMovie($id);
            if (!$movieResponse['success']) {
                return redirect()->route('admin.movies')->with('error', 'Không tìm thấy phim');
            }

            $movie = $movieResponse['data'];
            
            // Try to get additional movie data (genres, casts) if not included
            if (!isset($movie['genres']) || empty($movie['genres'])) {
                try {
                    // Try to get genres from a separate API call or from the movie data
                    $movieGenresResponse = $this->apiService->getMovieGenres($id);
                    if ($movieGenresResponse['success'] && isset($movieGenresResponse['data'])) {
                        $movie['genres'] = $movieGenresResponse['data'];
                    } else {
                        // Fallback: try to extract genres from movie data if available
                        if (isset($movie['genre']) && is_array($movie['genre'])) {
                            $movie['genres'] = array_map(function($genre) {
                                return is_string($genre) ? ['name' => $genre] : $genre;
                            }, $movie['genre']);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not fetch movie genres: ' . $e->getMessage());
                    // Fallback: try to extract genres from movie data if available
                    if (isset($movie['genre']) && is_array($movie['genre'])) {
                        $movie['genres'] = array_map(function($genre) {
                            return is_string($genre) ? ['name' => $genre] : $genre;
                        }, $movie['genre']);
                    }
                }
            }
            
            if (!isset($movie['movie_casts']) || empty($movie['movie_casts'])) {
                try {
                    // Try to get casts from a separate API call
                    $movieCastsResponse = $this->apiService->getMovieCast($id);
                    if ($movieCastsResponse['success'] && isset($movieCastsResponse['data'])) {
                        $movie['movie_casts'] = $movieCastsResponse['data'];
                    } else {
                        // Fallback: try to extract casts from movie data if available
                        if (isset($movie['cast']) && is_array($movie['cast'])) {
                            $movie['movie_casts'] = array_map(function($cast) {
                                return is_string($cast) ? ['name' => $cast] : $cast;
                            }, $movie['cast']);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not fetch movie casts: ' . $e->getMessage());
                    // Fallback: try to extract casts from movie data if available
                    if (isset($movie['cast']) && is_array($movie['cast'])) {
                        $movie['movie_casts'] = array_map(function($cast) {
                            return is_string($cast) ? ['name' => $cast] : $cast;
                        }, $movie['cast']);
                    }
                }
            }
            
            // Load genres, directors, and actors for suggestions
            $genresResponse = $this->apiService->getGenres();
            $directorsResponse = $this->apiService->getDirectors();
            $actorsResponse = $this->apiService->getActors();
            
            $genres = $genresResponse['success'] ? $genresResponse['data'] : [];
            $directors = $directorsResponse['success'] ? $directorsResponse['data'] : [];
            $actors = $actorsResponse['success'] ? $actorsResponse['data'] : [];
            
            // Debug logging
            \Log::info('Movie data from API:', [
                'id' => $movie['id'] ?? 'unknown',
                'title' => $movie['title'] ?? 'unknown',
                'genres' => $movie['genres'] ?? 'no genres',
                'genres_count' => is_array($movie['genres'] ?? null) ? count($movie['genres']) : 0,
                'movie_casts' => $movie['movie_casts'] ?? 'no casts',
                'casts_count' => is_array($movie['movie_casts'] ?? null) ? count($movie['movie_casts']) : 0,
                'raw_genre' => $movie['genre'] ?? 'no raw genre',
                'raw_cast' => $movie['cast'] ?? 'no raw cast',
                'suggestions_count' => [
                    'genres' => count($genres),
                    'directors' => count($directors),
                    'actors' => count($actors)
                ]
            ]);
            
            return view('admin.movies.edit', compact('movie', 'user', 'genres', 'directors', 'actors'));

        } catch (\Exception $e) {
            Log::error('Admin edit movie error: ' . $e->getMessage());
            return redirect()->route('admin.movies')->with('error', 'Không thể tải thông tin phim');
        }
    }

    public function updateMovie(Request $request, $id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;

        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Validate request - more flexible for updates
            $request->validate([
                'title' => 'required|string|max:255',
                'title_vi' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'description_vi' => 'required|string|min:10',
                'director' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'language' => 'required|string|max:255',
                'release_date' => 'required|date',
                'duration' => 'required|integer|min:1|max:600',
                'rating' => 'required|numeric|min:-1|max:10',
                'genres' => 'required|array|min:1',
                'casts' => 'required|array|min:1',
                'poster' => 'nullable|file|image|max:5120',
                'backdrop' => 'nullable|file|image|max:5120',
                'trailer' => 'nullable|url',
            ], [
                'title.required' => 'Tên phim tiếng Anh là bắt buộc',
                'title_vi.required' => 'Tên phim tiếng Việt là bắt buộc',
                'description.required' => 'Mô tả là bắt buộc',
                'description.min' => 'Mô tả phải có ít nhất 10 ký tự',
                'description_vi.required' => 'Mô tả tiếng Việt là bắt buộc',
                'description_vi.min' => 'Mô tả tiếng Việt phải có ít nhất 10 ký tự',
                'director.required' => 'Đạo diễn là bắt buộc',
                'country.required' => 'Quốc gia là bắt buộc',
                'language.required' => 'Ngôn ngữ là bắt buộc',
                'release_date.required' => 'Ngày phát hành là bắt buộc',
                'duration.required' => 'Thời lượng là bắt buộc',
                'duration.min' => 'Thời lượng phải ít nhất 1 phút',
                'duration.max' => 'Thời lượng không được quá 600 phút',
                'rating.required' => 'Đánh giá là bắt buộc',
                'rating.numeric' => 'Đánh giá phải là số',
                'rating.min' => 'Đánh giá phải từ -1 đến 10',
                'rating.max' => 'Đánh giá phải từ -1 đến 10',
                'genres.required' => 'Phải chọn ít nhất 1 thể loại',
                'genres.min' => 'Phải chọn ít nhất 1 thể loại',
                'casts.required' => 'Phải có ít nhất 1 diễn viên',
                'casts.min' => 'Phải có ít nhất 1 diễn viên',
                'poster.image' => 'Poster phải là file ảnh',
                'poster.max' => 'Poster không được quá 5MB',
                'backdrop.image' => 'Backdrop phải là file ảnh',
                'backdrop.max' => 'Backdrop không được quá 5MB',
                'trailer.url' => 'URL trailer không hợp lệ',
            ]);

            // Prepare data for API
            $movieData = [
                'title' => $request->title,
                'title_vi' => $request->title_vi,
                'slug' => $request->slug ?? \Str::slug($request->title),
                'description' => $request->description,
                'description_vi' => $request->description_vi,
                'director' => $request->director,
                'country' => $request->country,
                'language' => $request->language,
                'release_date' => $request->release_date,
                'duration' => (int) $request->duration, // Convert to integer
                'rating' => (float) $request->rating, // Convert to float
                'featured' => $request->has('featured'),
                'trailer' => $request->trailer,
                'genre' => $request->input('genres', []), // API expects 'genre' not 'genres'
                'cast' => $request->input('casts', []), // API expects 'cast' not 'casts'
            ];

            // Handle file uploads - check for Google Drive URLs first
            if ($request->filled('poster_url')) {
                // Use Google Drive URL from upload
                $movieData['poster'] = $request->poster_url;
            } elseif ($request->hasFile('poster')) {
                // Use uploaded file
                $movieData['poster'] = $request->file('poster');
            }
            
            if ($request->filled('backdrop_url')) {
                // Use Google Drive URL from upload
                $movieData['backdrop'] = $request->backdrop_url;
            } elseif ($request->hasFile('backdrop')) {
                // Use uploaded file
                $movieData['backdrop'] = $request->file('backdrop');
            }

            // Since API doesn't have update endpoint, we'll simulate the update
            // In a real application, you would call the API or update database directly
            
            // Log the update data for debugging
            \Log::info('Movie update request:', [
                'movie_id' => $id,
                'data_count' => count($movieData),
                'genres_count' => count($movieData['genre'] ?? []),
                'casts_count' => count($movieData['cast'] ?? []),
                'poster_url' => $request->poster_url ?? 'not set',
                'backdrop_url' => $request->backdrop_url ?? 'not set',
                'poster_file' => $request->hasFile('poster') ? 'has file' : 'no file',
                'backdrop_file' => $request->hasFile('backdrop') ? 'has file' : 'no file',
                'final_poster' => $movieData['poster'] ?? 'not set',
                'final_backdrop' => $movieData['backdrop'] ?? 'not set',
                'request_data' => $request->all(),
                'movie_data' => $movieData
            ]);

            // Call API to update movie
            $startTime = microtime(true);
            $updateResponse = $this->apiService->updateMovie($id, $movieData);
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);
            
            \Log::info('Movie update response:', [
                'movie_id' => $id,
                'response_time_ms' => $responseTime,
                'success' => $updateResponse['success'] ?? false,
                'message' => $updateResponse['message'] ?? 'No message'
            ]);
            
            if ($updateResponse['success']) {
                // Redirect back to edit page to show updated information
                return redirect()->route('admin.movies.edit', $id)->with('success', 'Cập nhật phim thành công!');
            } else {
                $errorMessage = $updateResponse['message'] ?? 'Có lỗi xảy ra khi cập nhật phim';
                
                // Log the full response for debugging
                \Log::error('API Update Movie Response:', $updateResponse);
                
                // If it's a validation error, show detailed errors
                if (isset($updateResponse['errors'])) {
                    $errors = [];
                    foreach ($updateResponse['errors'] as $field => $fieldErrors) {
                        if (is_array($fieldErrors)) {
                            $errors = array_merge($errors, $fieldErrors);
                        } else {
                            $errors[] = $fieldErrors;
                        }
                    }
                    $errorMessage = 'Lỗi validation: ' . implode(', ', $errors);
                }
                
                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in updateMovie:', [
                'errors' => $e->errors(),
                'movie_id' => $id
            ]);
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Admin update movie error: ' . $e->getMessage(), [
                'movie_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Cập nhật phim thất bại: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Users Management
     */
    public function users()
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Get users from API
            $usersResponse = $this->apiService->getUsers();
            
            \Log::info('Users API Response:', [
                'success' => $usersResponse['success'] ?? false,
                'data_count' => count($usersResponse['data'] ?? []),
                'response' => $usersResponse
            ]);
            
            // Handle different API response formats
            $users = [];
            if ($usersResponse['success']) {
                $data = $usersResponse['data'] ?? [];
                // Handle paginated response
                if (isset($data['data']) && is_array($data['data'])) {
                    $users = $data['data'];
                } elseif (is_array($data)) {
                    $users = $data;
                }
                
                // Enhance users with avatar information
                foreach ($users as &$user) {
                    // Get user details to fetch avatar
                    try {
                        $userDetailResponse = $this->apiService->getUser($user['id']);
                        if ($userDetailResponse['success']) {
                            $userDetail = $userDetailResponse['data'];
                            $user['avatar'] = $userDetail['avatar'] ?? null;
                            $user['avatar_url'] = $userDetail['avatar_url'] ?? null;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Could not fetch user avatar for user ' . $user['id'] . ': ' . $e->getMessage());
                    }
                }
            }
            
            // If no users from API, use test data for development
            if (empty($users)) {
                \Log::warning('No users from API, using test data');
                $users = [
                    [
                        'id' => 1,
                        'name' => 'tran minh tri',
                        'email' => 'tritranminh484@gmail.com',
                        'role' => 'admin',
                        'is_active' => true,
                        'avatar' => 'uploads/avatars/avatar_1.jpg',
                        'avatar_url' => 'uploads/avatars/avatar_1.jpg',
                        'created_at' => '2025-08-31T07:43:48.000000Z'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Super Admin',
                        'email' => 'superadmin@example.com',
                        'role' => 'super_admin',
                        'is_active' => true,
                        'created_at' => '2025-08-31T03:38:14.000000Z'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Nguyen Thanh Nam',
                        'email' => 'trinhdangthanhnam9@gmail.com',
                        'role' => 'user',
                        'is_active' => true,
                        'created_at' => '2025-10-13T12:17:50.000000Z'
                    ],
                    [
                        'id' => 4,
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                        'role' => 'user',
                        'is_active' => false,
                        'created_at' => '2025-08-31T03:38:15.000000Z'
                    ]
                ];
            }

            return view('admin.users.index', compact('users', 'user'));

        } catch (\Exception $e) {
            Log::error('Admin users error: ' . $e->getMessage());
            
            // Use test data if API fails
            $users = [
                [
                    'id' => 1,
                    'name' => 'tran minh tri',
                    'email' => 'tritranminh484@gmail.com',
                    'role' => 'admin',
                    'is_active' => true,
                    'created_at' => '2025-08-31T07:43:48.000000Z'
                ],
                [
                    'id' => 2,
                    'name' => 'Super Admin',
                    'email' => 'superadmin@example.com',
                    'role' => 'super_admin',
                    'is_active' => true,
                    'created_at' => '2025-08-31T03:38:14.000000Z'
                ],
                [
                    'id' => 3,
                    'name' => 'Nguyen Thanh Nam',
                    'email' => 'trinhdangthanhnam9@gmail.com',
                    'role' => 'user',
                    'is_active' => true,
                    'created_at' => '2025-10-13T12:17:50.000000Z'
                ]
            ];
            
            return view('admin.users.index', compact('users', 'user'));
        }
    }

    /**
     * Assign role to user
     */
    public function assignUserRole(Request $request, $id)
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện hành động này'], 401);
        }
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }

        try {
            $role = $request->input('role');
            
            \Log::info('Assign role request:', [
                'user_id' => $id,
                'role' => $role,
                'request_data' => $request->all()
            ]);
            
            // Call API to assign role
            $response = $this->apiService->assignUserRole($id, $role);
            
            \Log::info('Assign role API response:', [
                'user_id' => $id,
                'role' => $role,
                'response' => $response
            ]);
            
            if ($response['success']) {
                return response()->json(['success' => true, 'message' => 'Cấp quyền thành công!']);
            } else {
                $errorMessage = $response['message'] ?? 'Có lỗi xảy ra';
                if (isset($response['errors'])) {
                    $errorMessage .= ': ' . implode(', ', $response['errors']);
                }
                return response()->json(['success' => false, 'message' => $errorMessage]);
            }

        } catch (\Exception $e) {
            Log::error('Admin assign role error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi cấp quyền'], 500);
        }
    }

    /**
     * Revoke admin role from user
     */
    public function revokeUserAdminRole($id)
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện hành động này'], 401);
        }
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Chỉ Admin mới có quyền hủy quyền admin'], 403);
        }

        try {
            // Call API to revoke admin role
            $response = $this->apiService->revokeUserAdminRole($id);
            
            if ($response['success']) {
                return response()->json(['success' => true, 'message' => 'Hủy quyền admin thành công!']);
            } else {
                return response()->json(['success' => false, 'message' => $response['message'] ?? 'Có lỗi xảy ra']);
            }

        } catch (\Exception $e) {
            Log::error('Admin revoke role error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi hủy quyền'], 500);
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleUserStatus(Request $request, $id)
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện hành động này'], 401);
        }
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }

        try {
            $activate = $request->input('activate', true);
            
            // Call API to toggle user status
            $response = $this->apiService->toggleUserStatus($id, $activate);
            
            if ($response['success']) {
                $message = $activate ? 'Kích hoạt người dùng thành công!' : 'Tạm khóa người dùng thành công!';
                return response()->json(['success' => true, 'message' => $message]);
            } else {
                return response()->json(['success' => false, 'message' => $response['message'] ?? 'Có lỗi xảy ra']);
            }

        } catch (\Exception $e) {
            Log::error('Admin toggle user status error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi thay đổi trạng thái người dùng'], 500);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Chỉ Admin mới có quyền xóa người dùng'], 403);
        }

        try {
            // Call API to delete user
            $response = $this->apiService->deleteUser($id);
            
            if ($response['success']) {
                return response()->json(['success' => true, 'message' => 'Xóa người dùng thành công!']);
            } else {
                return response()->json(['success' => false, 'message' => $response['message'] ?? 'Có lỗi xảy ra']);
            }

        } catch (\Exception $e) {
            Log::error('Admin delete user error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa người dùng'], 500);
        }
    }

    /**
     * Export users to Excel
     */
    public function exportUsers()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Call API to export users
            $response = $this->apiService->exportUsers();
            
            if ($response['success']) {
                return response($response['data'])
                    ->header('Content-Type', 'text/csv; charset=UTF-8')
                    ->header('Content-Disposition', 'attachment; filename="danh_sach_nguoi_dung_' . date('Y-m-d') . '.csv"');
            } else {
                return redirect()->back()->with('error', $response['message'] ?? 'Có lỗi xảy ra khi xuất dữ liệu');
            }

        } catch (\Exception $e) {
            Log::error('Admin export users error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất dữ liệu');
        }
    }

    /**
     * View user details
     */
    public function viewUser($id)
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Call API to get user details
            $response = $this->apiService->getUser($id);
            
            if ($response['success']) {
                $userData = $response['data'];
                return view('admin.users.view', compact('userData', 'user'));
            } else {
                return redirect()->back()->with('error', $response['message'] ?? 'Không thể lấy thông tin người dùng');
            }

        } catch (\Exception $e) {
            Log::error('Admin view user error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xem thông tin người dùng');
        }
    }

    /**
     * Edit user form
     */
    public function editUser($id)
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Call API to get user details
            $response = $this->apiService->getUser($id);
            
            if ($response['success']) {
                $userData = $response['data'];
                return view('admin.users.edit', compact('userData', 'user'));
            } else {
                return redirect()->back()->with('error', $response['message'] ?? 'Không thể lấy thông tin người dùng');
            }

        } catch (\Exception $e) {
            Log::error('Admin edit user error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi lấy thông tin người dùng');
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission - Only admin
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này'], 403);
        }

        try {
            $userData = $request->only(['name', 'email', 'phone']);
            
            // Call API to update user
            $response = $this->apiService->updateUser($id, $userData);
            
            if ($response['success']) {
                return response()->json(['success' => true, 'message' => 'Cập nhật thông tin người dùng thành công!']);
            } else {
                return response()->json(['success' => false, 'message' => $response['message'] ?? 'Có lỗi xảy ra']);
            }

        } catch (\Exception $e) {
            Log::error('Admin update user error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật thông tin người dùng'], 500);
        }
    }

    /**
     * Reviews Management
     */
public function reviews()
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'review_manager', 'violation_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Get reviews from API
            $reviewsResponse = $this->apiService->getReviews();
            $reviews = $reviewsResponse['success'] ? $reviewsResponse['data'] : [];

            // Enhance reviews with movie and user information
            foreach ($reviews as &$review) {
                // Get movie information
                if (isset($review['movie_id'])) {
                    try {
                        $movieResponse = $this->apiService->getMovie($review['movie_id']);
                        if ($movieResponse['success']) {
                            $movie = $movieResponse['data'];
                            $review['movie_title'] = $movie['title'] ?? 'N/A';
                            $review['movie_title_vi'] = $movie['title_vi'] ?? 'N/A';
                            $review['movie_poster'] = $movie['poster'] ?? null;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Could not fetch movie for review: ' . $e->getMessage());
                    }
                }
                
                // Get user information
                if (isset($review['user_id'])) {
                    try {
                        $userResponse = $this->apiService->getUser($review['user_id']);
                        if ($userResponse['success']) {
                            $user = $userResponse['data'];
                            $review['user_name'] = $user['name'] ?? 'Người dùng ẩn danh';
                            $review['user_email'] = $user['email'] ?? 'N/A';
                            $review['user_avatar_url'] = $user['avatar'] ?? null;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Could not fetch user for review: ' . $e->getMessage());
                    }
                }
            }

            return view('admin.reviews.index', compact('reviews', 'user'));

        } catch (\Exception $e) {
            Log::error('Admin reviews error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Không thể tải danh sách đánh giá');
        }
    }

    /**
     * Load reviews via AJAX
     */
    public function loadReviews()
    {
        $user = session('user');
        
        // Check if user is logged in
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện hành động này'], 401);
        }
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'review_manager', 'violation_manager'])) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập trang này'], 403);
        }

        try {
            // Get reviews from API
            $reviewsResponse = $this->apiService->getReviews();
            $reviews = $reviewsResponse['success'] ? $reviewsResponse['data'] : [];

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
                'message' => 'Tải dữ liệu thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Admin load reviews error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách đánh giá'
            ], 500);
        }
    }

    /**
     * Comments Management
     */
    public function comments()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'review_manager', 'violation_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Get comments from API
            $commentsResponse = $this->apiService->getComments();
            $comments = $commentsResponse['success'] ? $commentsResponse['data'] : [];

            // API already returns user and movie details, no need to enrich
            // Just add movie title if missing
            if (is_array($comments)) {
                foreach ($comments as &$comment) {
                    // Add movie title if not present
                    if (!isset($comment['movie_title']) && isset($comment['movie_id'])) {
                        $movieResponse = $this->apiService->getMovie($comment['movie_id']);
                        if ($movieResponse['success'] && isset($movieResponse['data'])) {
                            $comment['movie_title'] = $movieResponse['data']['title_vi'] ?? $movieResponse['data']['title'] ?? 'N/A';
                        } else {
                            $comment['movie_title'] = 'N/A';
                        }
                    }
                }
            }

            return view('admin.comments.index', compact('comments', 'user'));

        } catch (\Exception $e) {
            Log::error('Admin comments error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Không thể tải danh sách bình luận');
        }
    }

    /**
     * Theaters Management
     */
    public function theaters()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $theatersResponse = $this->apiService->getTheaters();
            
            \Log::info('Theaters API Response:', $theatersResponse);
            
            $theaters = $theatersResponse['success'] ? $theatersResponse['data'] : [];
            
            \Log::info('Theaters data for view:', [
                'success' => $theatersResponse['success'],
                'count' => count($theaters),
                'first_theater' => $theaters[0] ?? null
            ]);

            // Clear any previous session errors to prevent showing old error messages
            session()->forget('error');
            
            return view('admin.theaters.index', compact('theaters', 'user'));

        } catch (\Exception $e) {
            Log::error('Admin theaters error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Không thể tải danh sách rạp chiếu');
        }
    }

    /**
     * Show create theater form
     */
    public function createTheater()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        return view('admin.theaters.create', compact('user'));
    }

    /**
     * Store new theater
     */
    public function storeTheater(Request $request)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Tên rạp chiếu là bắt buộc',
            'name.max' => 'Tên rạp chiếu không được quá 255 ký tự',
            'address.required' => 'Địa chỉ là bắt buộc',
            'address.max' => 'Địa chỉ không được quá 500 ký tự',
            'phone.max' => 'Số điện thoại không được quá 20 ký tự',
            'email.email' => 'Email không hợp lệ',
            'email.max' => 'Email không được quá 255 ký tự',
            'description.max' => 'Mô tả không được quá 1000 ký tự',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true)
            ];

            \Log::info('Creating theater with data:', $data);

            $response = $this->apiService->createTheater($data);

            \Log::info('Theater creation response:', $response);

            if ($response['success']) {
                return redirect()->route('admin.theaters')->with('success', 'Rạp chiếu đã được tạo thành công!');
            } else {
                $errorMessage = $response['message'] ?? 'Không thể tạo rạp chiếu';
                if (isset($response['errors'])) {
                    $errorMessage .= ': ' . implode(', ', $response['errors']);
                }
                return back()->with('error', $errorMessage)->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Theater creation error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tạo rạp chiếu: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show theater details
     */
    public function showTheater($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $theaterResponse = $this->apiService->getTheater($id);
            
            if (!$theaterResponse['success']) {
                return redirect()->route('admin.theaters')->with('error', 'Không tìm thấy rạp chiếu');
            }

            $theater = $theaterResponse['data'];

            return view('admin.theaters.show', compact('theater', 'user'));

        } catch (\Exception $e) {
            \Log::error('Admin show theater error: ' . $e->getMessage());
            return redirect()->route('admin.theaters')->with('error', 'Không thể tải thông tin rạp chiếu');
        }
    }

    /**
     * Show edit theater form
     */
    public function editTheater($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $theaterResponse = $this->apiService->getTheater($id);
            
            if (!$theaterResponse['success']) {
                return redirect()->route('admin.theaters')->with('error', 'Không tìm thấy rạp chiếu');
            }

            $theater = $theaterResponse['data'];

            return view('admin.theaters.edit', compact('theater', 'user'));

        } catch (\Exception $e) {
            \Log::error('Admin edit theater error: ' . $e->getMessage());
            return redirect()->route('admin.theaters')->with('error', 'Không thể tải thông tin rạp chiếu');
        }
    }

    /**
     * Update theater
     */
    public function updateTheater(Request $request, $id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Tên rạp chiếu là bắt buộc',
            'name.max' => 'Tên rạp chiếu không được quá 255 ký tự',
            'address.required' => 'Địa chỉ là bắt buộc',
            'address.max' => 'Địa chỉ không được quá 500 ký tự',
            'phone.max' => 'Số điện thoại không được quá 20 ký tự',
            'email.email' => 'Email không hợp lệ',
            'email.max' => 'Email không được quá 255 ký tự',
            'description.max' => 'Mô tả không được quá 1000 ký tự',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true)
            ];

            \Log::info('Updating theater with data:', $data);

            $response = $this->apiService->updateTheater($id, $data);

            \Log::info('Theater update response:', $response);

            if ($response['success']) {
                return redirect()->route('admin.theaters')->with('success', 'Thông tin rạp chiếu đã được cập nhật thành công!');
            } else {
                $errorMessage = $response['message'] ?? 'Không thể cập nhật rạp chiếu';
                if (isset($response['errors'])) {
                    $errorMessage .= ': ' . implode(', ', $response['errors']);
                }
                return back()->with('error', $errorMessage)->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Theater update error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật rạp chiếu: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete theater
     */
    public function destroyTheater($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $response = $this->apiService->deleteTheater($id);

            \Log::info('Delete theater response:', $response);

            if ($response['success']) {
                return redirect()->route('admin.theaters')->with('success', 'Rạp chiếu đã được xóa thành công!');
            } else {
                $errorMessage = $response['message'] ?? 'Không thể xóa rạp chiếu';
                
                // Handle specific error cases
                if (strpos($errorMessage, 'existing schedules') !== false) {
                    $errorMessage = 'Không thể xóa rạp chiếu vì còn có suất chiếu. Vui lòng xóa tất cả suất chiếu trước.';
                } elseif (strpos($errorMessage, 'not found') !== false) {
                    $errorMessage = 'Không tìm thấy rạp chiếu này.';
                }
                
                return redirect()->route('admin.theaters')->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            \Log::error('Theater deletion error: ' . $e->getMessage());
            return redirect()->route('admin.theaters')->with('error', 'Có lỗi xảy ra khi xóa rạp chiếu: ' . $e->getMessage());
        }
    }

    /**
     * Show create schedule form
     */
    public function createSchedule($theaterId)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            \Log::info('Creating schedule for theater ID:', ['theater_id' => $theaterId]);
            
            // Get theater details
            $theaterResponse = $this->apiService->getTheater($theaterId);
            \Log::info('Theater response:', $theaterResponse);
            
            if (!$theaterResponse['success']) {
                \Log::error('Theater not found:', $theaterResponse);
                return redirect()->route('admin.theaters')->with('error', 'Không tìm thấy rạp chiếu');
            }
            $theater = $theaterResponse['data'];

            // Get all movies - try public endpoint first
            $moviesResponse = $this->apiService->getMovies();
            $movies = [];
            
            \Log::info('Movies API Response for schedule creation:', $moviesResponse);
            
            if ($moviesResponse['success'] && isset($moviesResponse['data'])) {
                // Handle paginated response
                if (isset($moviesResponse['data']['data']) && is_array($moviesResponse['data']['data'])) {
                    $movies = $moviesResponse['data']['data'];
                } elseif (is_array($moviesResponse['data'])) {
                    $movies = $moviesResponse['data'];
                }
                
                \Log::info('Movies loaded for schedule creation:', [
                    'count' => count($movies),
                    'first_movie' => $movies[0] ?? null
                ]);
            } else {
                \Log::warning('Failed to get movies from public endpoint, trying admin endpoint', [
                    'response' => $moviesResponse
                ]);
                
                // Try admin movies endpoint as fallback
                try {
                    $adminMoviesResponse = $this->apiService->getAdminMovies();
                    \Log::info('Admin movies response:', $adminMoviesResponse);
                    
                    if ($adminMoviesResponse['success'] && isset($adminMoviesResponse['data'])) {
                        if (isset($adminMoviesResponse['data']['data']) && is_array($adminMoviesResponse['data']['data'])) {
                            $movies = $adminMoviesResponse['data']['data'];
                        } elseif (is_array($adminMoviesResponse['data'])) {
                            $movies = $adminMoviesResponse['data'];
                        }
                        \Log::info('Movies loaded from admin endpoint:', [
                            'count' => count($movies),
                            'first_movie' => $movies[0] ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to get movies from admin endpoint: ' . $e->getMessage());
                }
            }

            // If still no movies, create empty array to prevent errors
            if (empty($movies)) {
                \Log::warning('No movies found for schedule creation, using empty array');
                $movies = [];
            }

            \Log::info('Final data for create schedule view:', [
                'theater_id' => $theaterId,
                'theater_name' => $theater['name'] ?? 'N/A',
                'movies_count' => count($movies)
            ]);

            return view('admin.schedules.create', compact('theater', 'movies', 'user'));

        } catch (\Exception $e) {
            \Log::error('Admin create schedule error: ' . $e->getMessage());
            return redirect()->route('admin.theaters')->with('error', 'Không thể tải thông tin tạo suất chiếu');
        }
    }

    /**
     * Store new schedule
     */
    public function storeSchedule(Request $request, $theaterId)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');

        // Validate request
        $request->validate([
            'movie_id' => 'required|integer',
            'room_name' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,inactive'
        ], [
            'movie_id.required' => 'Phải chọn phim',
            'movie_id.integer' => 'Phim không hợp lệ',
            'room_name.required' => 'Tên phòng chiếu là bắt buộc',
            'room_name.max' => 'Tên phòng chiếu không được quá 255 ký tự',
            'start_time.required' => 'Thời gian bắt đầu là bắt buộc',
            'start_time.date' => 'Thời gian bắt đầu không hợp lệ',
            'start_time.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại',
            'price.required' => 'Giá vé là bắt buộc',
            'price.numeric' => 'Giá vé phải là số',
            'price.min' => 'Giá vé phải lớn hơn hoặc bằng 0',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        try {
            $data = [
                'movie_id' => $request->movie_id,
                'theater_id' => $theaterId,
                'room_name' => $request->room_name,
                'start_time' => $request->start_time,
                'price' => $request->price,
                'status' => $request->status ?? 'active'
            ];

            \Log::info('Creating schedule with data:', $data);

            $response = $this->apiService->createSchedule($data);

            \Log::info('Schedule creation response:', $response);

            if ($response['success']) {
                return redirect()->route('admin.theaters.show', $theaterId)->with('success', 'Suất chiếu đã được tạo thành công!');
            } else {
                $errorMessage = $response['message'] ?? 'Không thể tạo suất chiếu';
                if (isset($response['errors'])) {
                    $errorMessage .= ': ' . implode(', ', $response['errors']);
                }
                return back()->with('error', $errorMessage)->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Schedule creation error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tạo suất chiếu: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Violations Management
     */
    public function violations()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'violation_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Get violations from API
            $violationsResponse = $this->apiService->getViolations();
            $violations = $violationsResponse['success'] ? $violationsResponse['data'] : [];

            return view('admin.violations.index', compact('violations', 'user'));

        } catch (\Exception $e) {
            Log::error('Admin violations error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Không thể tải danh sách vi phạm');
        }
    }

    /**
     * Reports
     */
    public function reports()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'review_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Get reports data from API
            $reportsData = [
                'movies_stats' => $this->getApiData($this->apiService->getMoviesStats()),
                'users_stats' => $this->getApiData($this->apiService->getUsersStats()),
                'reviews_stats' => $this->getApiData($this->apiService->getReviewsStats()),
                'bookings_stats' => $this->getApiData($this->apiService->getBookingsStats()),
                'most_viewed_movies' => $this->getApiData($this->apiService->getMostViewedMovies()),
                'monthly_revenue' => $this->getApiData($this->apiService->getMonthlyRevenue())
            ];

            return view('admin.reports.index', compact('reportsData', 'user'));

        } catch (\Exception $e) {
            Log::error('Admin reports error: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Không thể tải báo cáo thống kê');
        }
    }

    /**
     * Export statistics report
     */
    public function exportReport()
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission - bypass for now due to session issues
        if ($user && !in_array($user['role'], ['admin', 'review_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Get all statistics data
            $moviesStats = $this->getApiData($this->apiService->getMoviesStats());
            $usersStats = $this->getApiData($this->apiService->getUsersStats());
            $reviewsStats = $this->getApiData($this->apiService->getReviewsStats());
            $bookingsStats = $this->getApiData($this->apiService->getBookingsStats());
            $mostViewedMovies = $this->getApiData($this->apiService->getMostViewedMovies());
            $monthlyRevenue = $this->getApiData($this->apiService->getMonthlyRevenue());
            
            // Create CSV content
            $csvContent = "BÁO CÁO THỐNG KÊ HỆ THỐNG CINEMA\n";
            $csvContent .= "Ngày xuất báo cáo: " . date('d/m/Y H:i:s') . "\n\n";
            
            // Movies Statistics
            $csvContent .= "=== THỐNG KÊ PHIM ===\n";
            $csvContent .= "Tổng số phim," . ($moviesStats['total'] ?? 0) . "\n";
            $csvContent .= "Phim nổi bật," . ($moviesStats['featured'] ?? 0) . "\n";
            $csvContent .= "Phim mới tháng này," . ($moviesStats['this_month'] ?? 0) . "\n\n";
            
            // Users Statistics
            $csvContent .= "=== THỐNG KÊ NGƯỜI DÙNG ===\n";
            $csvContent .= "Tổng người dùng," . ($usersStats['total'] ?? 0) . "\n";
            $csvContent .= "Đăng ký tháng này," . ($usersStats['this_month'] ?? 0) . "\n";
            $csvContent .= "Người dùng hoạt động," . ($usersStats['active'] ?? 0) . "\n\n";
            
            // Reviews Statistics
            $csvContent .= "=== THỐNG KÊ ĐÁNH GIÁ ===\n";
            $csvContent .= "Tổng đánh giá," . ($reviewsStats['total'] ?? 0) . "\n";
            $csvContent .= "Điểm trung bình," . number_format($reviewsStats['average'] ?? 0, 1) . "/5\n";
            $csvContent .= "Đánh giá tháng này," . ($reviewsStats['this_month'] ?? 0) . "\n\n";
            
            // Bookings Statistics
            $csvContent .= "=== THỐNG KÊ ĐẶT VÉ ===\n";
            $csvContent .= "Tổng vé đã bán," . ($bookingsStats['total_tickets'] ?? 0) . "\n";
            $csvContent .= "Doanh thu tháng này," . number_format($bookingsStats['this_month_revenue'] ?? 0) . "₫\n";
            $csvContent .= "Vé hôm nay," . ($bookingsStats['today_tickets'] ?? 0) . "\n\n";
            
            // Most Viewed Movies
            $csvContent .= "=== PHIM ĐƯỢC XEM NHIỀU NHẤT ===\n";
            $csvContent .= "STT,Tên phim,Số đánh giá,Điểm trung bình\n";
            if (!empty($mostViewedMovies)) {
                foreach ($mostViewedMovies as $index => $movie) {
                    $csvContent .= ($index + 1) . ",\"" . ($movie['title_vi'] ?? $movie['title'] ?? 'N/A') . "\"," . 
                                   ($movie['reviews_count'] ?? 0) . "," . number_format($movie['rating'] ?? 0, 1) . "/5\n";
                }
            } else {
                $csvContent .= "Không có dữ liệu\n";
            }
            
            // Monthly Revenue
            $csvContent .= "\n=== DOANH THU THEO THÁNG ===\n";
            $csvContent .= "Tháng,Doanh thu\n";
            if (!empty($monthlyRevenue)) {
                foreach ($monthlyRevenue as $month) {
                    $csvContent .= "\"" . ($month['month'] ?? 'N/A') . "\"," . number_format($month['revenue'] ?? 0) . "₫\n";
                }
            } else {
                $csvContent .= "Không có dữ liệu\n";
            }
            
            // Add BOM for UTF-8
            $bom = "\xEF\xBB\xBF";
            $csvContent = $bom . $csvContent;
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="bao_cao_thong_ke_' . date('Y-m-d') . '.csv"');

        } catch (\Exception $e) {
            Log::error('Admin export report error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất báo cáo');
        }
    }

    /**
     * Helper method to extract data from API response
     */
    private function getApiData($apiResponse)
    {
        if ($apiResponse && isset($apiResponse['success']) && $apiResponse['success']) {
            return $apiResponse['data'] ?? [];
        }
        return [];
    }

    /**
     * Show the form for creating a new movie.
     */
    public function newMovie()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;

        $user = session('user');
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $genresResponse = $this->apiService->getGenres();
            $directorsResponse = $this->apiService->getDirectors();
            $actorsResponse = $this->apiService->getActors();

            $genres = $genresResponse['success'] ? $genresResponse['data'] : [];
            $directors = $directorsResponse['success'] ? $directorsResponse['data'] : [];
            $actors = $actorsResponse['success'] ? $actorsResponse['data'] : [];

            return view('admin.movies.new', compact('user', 'genres', 'directors', 'actors'));
        } catch (\Exception $e) {
            Log::error('Admin new movie error: ' . $e->getMessage());
            return redirect()->route('admin.movies')->with('error', 'Không thể tải trang thêm phim mới');
        }
    }

    /**
     * Store a newly created movie in storage.
     */
    public function createMovie(Request $request)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;

        $user = session('user');
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            // Validate request data
            $request->validate([
                'title' => 'required|string|max:255',
                'title_vi' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'description_vi' => 'required|string|min:10',
                'poster' => 'required|file|image|max:5120',
                'backdrop' => 'required|file|image|max:5120',
                'trailer' => 'nullable|url',
                'release_date' => 'required|date',
                'duration' => 'required|integer|min:1|max:600',
                'genre' => 'required|string',
                'country' => 'required|string|max:255',
                'language' => 'required|string|max:255',
                'director' => 'required|string|max:255',
                'cast' => 'required|string',
                'slug' => 'required|string|max:255',
                'rating' => 'nullable|numeric|min:-1|max:10',
                'featured' => 'boolean',
            ], [
                'title.required' => 'Tên phim tiếng Anh là bắt buộc',
                'title_vi.required' => 'Tên phim tiếng Việt là bắt buộc',
                'description.required' => 'Mô tả tiếng Anh là bắt buộc',
                'description.min' => 'Mô tả phải có ít nhất 10 ký tự',
                'description_vi.required' => 'Mô tả tiếng Việt là bắt buộc',
                'description_vi.min' => 'Mô tả tiếng Việt phải có ít nhất 10 ký tự',
                'poster.required' => 'File poster là bắt buộc',
                'poster.file' => 'Poster phải là file',
                'poster.image' => 'Poster phải là file ảnh',
                'poster.max' => 'File poster không được quá 5MB',
                'backdrop.required' => 'File backdrop là bắt buộc',
                'backdrop.file' => 'Backdrop phải là file',
                'backdrop.image' => 'Backdrop phải là file ảnh',
                'backdrop.max' => 'File backdrop không được quá 5MB',
                'trailer.url' => 'URL trailer không hợp lệ',
                'release_date.required' => 'Ngày phát hành là bắt buộc',
                'release_date.date' => 'Ngày phát hành không hợp lệ',
                'duration.required' => 'Thời lượng là bắt buộc',
                'duration.integer' => 'Thời lượng phải là số nguyên',
                'duration.min' => 'Thời lượng phải lớn hơn 0 phút',
                'duration.max' => 'Thời lượng không được quá 600 phút',
                'genre.required' => 'Phải chọn ít nhất 1 thể loại',
                'country.required' => 'Quốc gia là bắt buộc',
                'language.required' => 'Ngôn ngữ là bắt buộc',
                'director.required' => 'Đạo diễn là bắt buộc',
                'cast.required' => 'Phải có ít nhất 1 diễn viên',
                'slug.required' => 'Slug là bắt buộc',
                'rating.numeric' => 'Đánh giá phải là số',
                'rating.min' => 'Đánh giá phải từ -1 đến 10',
                'rating.max' => 'Đánh giá phải từ -1 đến 10',
            ]);

            $movieData = $request->all();

            $createResponse = $this->apiService->createMovie($movieData);

            if ($createResponse['success']) {
                return redirect()->route('admin.movies')->with('success', 'Thêm phim mới thành công!');
            } else {
                $errorMessage = $createResponse['message'] ?? 'Có lỗi xảy ra khi thêm phim mới';
                
                // Log the full response for debugging
                \Log::error('API Create Movie Response:', $createResponse);
                
                // If it's a validation error, show detailed errors
                if (isset($createResponse['errors'])) {
                    $errors = [];
                    foreach ($createResponse['errors'] as $field => $fieldErrors) {
                        if (is_array($fieldErrors)) {
                            $errors = array_merge($errors, $fieldErrors);
                        } else {
                            $errors[] = $fieldErrors;
                        }
                    }
                    $errorMessage = 'Lỗi validation: ' . implode(', ', $errors);
                }
                
                return redirect()->back()->with('error', $errorMessage)->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Admin create movie error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm phim mới: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteMovie($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;

        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.movies')->with('error', 'Bạn không có quyền xóa phim');
        }

        try {
            $deleteResponse = $this->apiService->deleteMovie($id);

            if ($deleteResponse['success']) {
                return redirect()->route('admin.movies')->with('success', 'Xóa phim thành công!');
            } else {
                return redirect()->route('admin.movies')->with('error', $deleteResponse['message'] ?? 'Có lỗi xảy ra khi xóa phim');
            }
        } catch (\Exception $e) {
            Log::error('Admin delete movie error: ' . $e->getMessage());
            return redirect()->route('admin.movies')->with('error', 'Có lỗi xảy ra khi xóa phim: ' . $e->getMessage());
        }
    }

    /**
     * Show schedule details
     */
    public function showSchedule($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $scheduleResponse = $this->apiService->getSchedule($id);
            
            if (!$scheduleResponse['success']) {
                return redirect()->route('admin.theaters')->with('error', 'Không tìm thấy suất chiếu');
            }

            $schedule = $scheduleResponse['data'];

            return view('admin.schedules.show', compact('schedule', 'user'));

        } catch (\Exception $e) {
            \Log::error('Admin show schedule error: ' . $e->getMessage());
            return redirect()->route('admin.theaters')->with('error', 'Không thể tải thông tin suất chiếu');
        }
    }

    /**
     * Show edit schedule form
     */
    public function editSchedule($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $scheduleResponse = $this->apiService->getSchedule($id);
            
            if (!$scheduleResponse['success']) {
                return redirect()->route('admin.theaters')->with('error', 'Không tìm thấy suất chiếu');
            }

            $schedule = $scheduleResponse['data'];

            // Get theater details
            $theaterResponse = $this->apiService->getTheater($schedule['theater_id']);
            $theater = $theaterResponse['success'] ? $theaterResponse['data'] : null;

            // Get all movies
            $moviesResponse = $this->apiService->getMovies();
            $movies = [];
            
            if ($moviesResponse['success'] && isset($moviesResponse['data'])) {
                if (isset($moviesResponse['data']['data']) && is_array($moviesResponse['data']['data'])) {
                    $movies = $moviesResponse['data']['data'];
                } elseif (is_array($moviesResponse['data'])) {
                    $movies = $moviesResponse['data'];
                }
            }

            return view('admin.schedules.edit', compact('schedule', 'theater', 'movies', 'user'));

        } catch (\Exception $e) {
            \Log::error('Admin edit schedule error: ' . $e->getMessage());
            return redirect()->route('admin.theaters')->with('error', 'Không thể tải thông tin suất chiếu');
        }
    }

    /**
     * Update schedule
     */
    public function updateSchedule(Request $request, $id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'room_name' => 'required|string|max:255',
            'start_time' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:active,inactive'
        ]);

        try {
            $data = $request->only(['movie_id', 'room_name', 'start_time', 'price', 'status']);
            $data['theater_id'] = $request->input('theater_id');
            
            $response = $this->apiService->updateSchedule($id, $data);

            if ($response['success']) {
                return redirect()->route('admin.schedules.show', $id)->with('success', 'Suất chiếu đã được cập nhật thành công!');
            } else {
                return redirect()->back()->with('error', $response['message'] ?? 'Không thể cập nhật suất chiếu');
            }

        } catch (\Exception $e) {
            \Log::error('Schedule update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật suất chiếu: ' . $e->getMessage());
        }
    }

    /**
     * Delete schedule
     */
    public function destroySchedule($id)
    {
        // Check admin access
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck) return $accessCheck;
        
        $user = session('user');
        
        // Check permission
        if (!in_array($user['role'], ['admin', 'movie_manager'])) {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này');
        }

        try {
            $response = $this->apiService->deleteSchedule($id);

            if ($response['success']) {
                return redirect()->route('admin.theaters')->with('success', 'Suất chiếu đã được xóa thành công!');
            } else {
                return redirect()->back()->with('error', $response['message'] ?? 'Không thể xóa suất chiếu');
            }

        } catch (\Exception $e) {
            \Log::error('Schedule deletion error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa suất chiếu: ' . $e->getMessage());
        }
    }
}
