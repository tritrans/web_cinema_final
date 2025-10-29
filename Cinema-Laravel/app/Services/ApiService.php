<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiService
{
    private $baseUrl;
    private $token;

    /**
     * Khởi tạo ApiService
     * Thiết lập base URL và headers mặc định
     */
    public function __construct()
    {
        $this->baseUrl = config('app.api_url', 'http://127.0.0.1:8000/api');
        // Prioritize session over cookie
        $this->token = Session::get('jwt_token') ?? request()->cookie('jwt_token');
    }

    public function isAuthenticated()
    {
        $hasToken = !empty($this->token);
        $hasUser = !empty(Session::get('user'));return $hasToken || $hasUser;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    private function getHeaders()
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        return $headers;
    }

    private function handleResponse($response)
    {
        try {
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data['data'] ?? $data,
                    'message' => $data['message'] ?? null,
                    'status_code' => $response->status()
                ];
            }

            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'Request failed';
            
            // Handle specific error cases
            if ($response->status() === 401) {
                $this->clearAuth();
                $errorMessage = 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.';
            } elseif ($response->status() === 403) {
                $errorMessage = 'Bạn không có quyền thực hiện hành động này.';
            } elseif ($response->status() === 404) {
                $errorMessage = 'Không tìm thấy dữ liệu yêu cầu.';
            } elseif ($response->status() === 422) {
                $errorMessage = 'Dữ liệu không hợp lệ.';
            } elseif ($response->status() >= 500) {
                $errorMessage = 'Lỗi máy chủ. Vui lòng thử lại sau.';
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'error' => $errorData['error'] ?? 'HTTP ' . $response->status(),
                'status_code' => $response->status(),
                'errors' => $errorData['errors'] ?? null
            ];
        } catch (\Exception $e) {return [
                'success' => false,
                'message' => 'Không thể xử lý phản hồi từ máy chủ',
                'error' => $e->getMessage(),
                'status_code' => $response->status()
            ];
        }
    }

    /**
     * Clear authentication data
     */
    private function clearAuth()
    {
        Session::forget(['jwt_token', 'user']);
        $this->token = null;
    }

    /**
     * Make API request with retry logic
     */
    private function makeRequest($method, $endpoint, $data = [], $retries = 1)
    {
        $attempt = 0;
        $startTime = microtime(true);
        $maxExecutionTime = 10; // Maximum 10 seconds total
        
        while ($attempt <= $retries) {
            // Check if we've exceeded maximum execution time
            if (microtime(true) - $startTime > $maxExecutionTime) {return [
                    'success' => false,
                    'message' => 'Request timeout - quá thời gian chờ',
                    'error' => 'Execution timeout exceeded'
                ];
            }
            
            try {
                $response = Http::withHeaders($this->getHeaders())
                    ->timeout(30) // Increased to 30 seconds for potentially long operations like email sending
                    ->$method($this->baseUrl . $endpoint, $data);

                $result = $this->handleResponse($response);
                
                // If successful or non-retryable error, return immediately
                if ($result['success'] || !$this->shouldRetry($result)) {
                    return $result;
                }
                
                // If retryable error and we have attempts left, wait and retry
                if ($attempt < $retries) {
                    $waitTime = min(1000, pow(2, $attempt) * 100); // Max 1 second wait
                    usleep($waitTime * 1000);
                    $attempt++;
                    continue;
                }
                
                // No more retries, return the error
                return $result;
                
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                if ($attempt < $retries) {
                    $waitTime = min(1000, pow(2, $attempt) * 100); // Max 1 second wait
                    usleep($waitTime * 1000);
                    $attempt++;
                    continue;
                }
                
                return [
                    'success' => false,
                    'message' => 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.',
                    'error' => $e->getMessage()
                ];
            } catch (\Exception $e) {return [
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi gọi API',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // This should never be reached, but just in case
        return [
            'success' => false,
            'message' => 'Đã hết số lần thử lại',
            'error' => 'Max retries exceeded'
        ];
    }

    /**
     * Check if error should be retried
     */
    private function shouldRetry($result)
    {
        $retryableStatuses = [500, 502, 503, 504, 408, 429];
        return in_array($result['status_code'] ?? 0, $retryableStatuses);
    }

    /**
     * Đăng nhập người dùng
     * 
     * @param array $credentials - Thông tin đăng nhập (email, password)
     * @return array - Kết quả đăng nhập
     */
    public function login($credentials)
    {
        $result = $this->makeRequest('post', '/auth/login', $credentials);
        
        if ($result['success'] && isset($result['data']['access_token'])) {
            Session::put('jwt_token', $result['data']['access_token']);
            Session::put('user', $result['data']['user']);
            $this->token = $result['data']['access_token'];
        }

        return $result;
    }

    public function register($userData)
    {
        $result = $this->makeRequest('post', '/auth/register', $userData);
        
        if ($result['success'] && isset($result['data']['access_token'])) {
            Session::put('jwt_token', $result['data']['access_token']);
            Session::put('user', $result['data']['user']);
            $this->token = $result['data']['access_token'];
        }

        return $result;
    }

    public function logout()
    {
        if ($this->token) {
            $this->makeRequest('post', '/auth/logout');
        }

        $this->clearAuth();
    }

    public function getCurrentUser()
    {
        if (!$this->token) {
            return ['success' => false, 'message' => 'No authentication token'];
        }

        return $this->makeRequest('get', '/auth/me');
    }

    // Movie methods
    public function getMovies()
    {
        return $this->makeRequest('get', '/movies');
    }

    public function getAdminMovies()
    {
        return $this->makeRequest('get', '/admin/movies');
    }

    public function getMovie($id)
    {
        return $this->makeRequest('get', '/movies/' . $id);
    }

    public function updateMovie($id, $data)
    {
        // Check if token is valid, if not try to refresh
        if (!$this->token || $this->isTokenExpired()) {$refreshResult = $this->refreshToken();
            if (!$refreshResult['success']) {
                return [
                    'success' => false,
                    'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
                    'data' => null
                ];
            }
        }

        // Separate files from regular data
        $files = [];
        $formData = [];
        
        foreach ($data as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $files[$key] = $value;
            } else {
                // For arrays, send as multiple form fields with same name
                if (is_array($value)) {
                    // Don't add to formData here, we'll handle arrays separately
                    continue;
                } else {
                    $formData[$key] = (string)$value;
                }
            }
        }

        // Build multipart data
        $multipart = [];
        
        // Add regular form data
        foreach ($formData as $key => $value) {
            if ($value !== null && $value !== '') {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
        }
        
        // Add array data as multiple form fields
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $multipart[] = [
                        'name' => $key . '[]', // Add [] for array fields
                        'contents' => (string)$item,
                    ];
                }
            }
        }
        
        // Add files
        foreach ($files as $key => $file) {
            $multipart[] = [
                'name' => $key,
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        // Call real API endpoint
        try {
            // Use multipart for file uploads, JSON for regular updates
            if (!empty($files)) {
                // Has files, use Laravel HTTP client with asMultipart
                // Convert multipart array to Laravel HTTP format
                $multipartData = [];
                foreach ($multipart as $item) {
                    if (isset($item['contents']) && is_resource($item['contents'])) {
                        // This is a file
                        $multipartData[] = [
                            'name' => $item['name'],
                            'contents' => $item['contents'],
                            'filename' => $item['filename']
                        ];
                    } else {
                        // This is regular form data
                        $multipartData[] = [
                            'name' => $item['name'],
                            'contents' => $item['contents']
                        ];
                    }
                }
                
                $response = Http::withHeaders($this->getHeaders())
                    ->asMultipart()
                    ->timeout(60)
                    ->post($this->baseUrl . '/movies/' . $id . '/with-files', $multipartData);
            } else {
                // No files, use JSON
                $response = Http::withHeaders($this->getHeaders())
                    ->asJson()
                    ->timeout(60) // 60 seconds timeout
                    ->put($this->baseUrl . '/movies/' . $id, $data);
            }
            
            if ($response->successful()) {
                return $this->handleResponse($response);
            } else {
                return [
                    'success' => false,
                    'message' => 'Cập nhật phim thất bại: ' . ($response->json()['message'] ?? 'Unknown error'),
                    'data' => null
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {return [
                'success' => false,
                'message' => 'Kết nối API bị timeout. Vui lòng thử lại.',
                'data' => null
            ];
        } catch (\Exception $e) {return [
                'success' => false,
                'message' => 'Cập nhật phim thất bại: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Check if JWT token is expired
     */
    private function isTokenExpired()
    {
        if (!$this->token) {
            return true;
        }

        try {
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], explode('.', $this->token)[1])), true);
            $exp = $payload['exp'] ?? 0;
            return $exp < time();
        } catch (\Exception $e) {return true;
        }
    }

    /**
     * Refresh JWT token by re-login
     */
    private function refreshToken()
    {
        // For now, just return false to force re-login
        // In a real application, you would implement proper token refresh
        return ['success' => false, 'message' => 'Token expired, please login again'];
    }

    public function getFeaturedMovies()
    {
        return $this->makeRequest('get', '/movies/featured');
    }

    public function getMovieBySlug($slug)
    {
        // First get all movies to find the one with matching slug
        $response = $this->getMovies();
        
        if ($response['success'] && isset($response['data'])) {
            $movies = $response['data'];
            
            // Handle paginated response
            if (isset($movies['data']) && is_array($movies['data'])) {
                $movies = $movies['data'];
            }
            
            // Find movie by slug
            $movie = collect($movies)->firstWhere('slug', $slug);
            
            if ($movie) {
                // Try to get cast data
                try {
                    $castResponse = $this->getMovieCast($movie['id']);
                    if ($castResponse['success'] && isset($castResponse['data'])) {
                        $cast = is_array($castResponse['data']) ? $castResponse['data'] : [];
                        $movie['cast'] = array_column($cast, 'name');
                    }
                } catch (\Exception $e) {
                    // If cast API fails, keep original cast data
                }
                
                return [
                    'success' => true,
                    'data' => $movie
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Movie not found'
        ];
    }

    public function getMovieCast($movieId)
    {
        return $this->makeRequest('get', '/movies/' . $movieId . '/cast');
    }

    public function getMovieGenres($movieId)
    {
        return $this->makeRequest('get', '/movies/' . $movieId . '/genres');
    }

    // Theater management methods
    public function getAdminTheaters()
    {
        return $this->makeRequest('get', '/admin/theaters');
    }

    public function createTheater($data)
    {
        return $this->makeRequest('post', '/admin/theaters', $data);
    }

    public function updateTheater($theaterId, $data)
    {
        return $this->makeRequest('put', '/admin/theaters/' . $theaterId, $data);
    }

    public function deleteTheater($theaterId)
    {
        return $this->makeRequest('delete', '/admin/theaters/' . $theaterId);
    }

    public function getTheater($theaterId)
    {
        // Use public endpoint instead of admin endpoint to avoid auth issues
        return $this->makeRequest('get', '/theaters/' . $theaterId);
    }

    public function createSchedule($data)
    {
        return $this->makeRequest('post', '/schedules', $data);
    }

    public function getSchedule($id)
    {
        return $this->makeRequest('get', '/schedules/' . $id);
    }

    public function updateSchedule($id, $data)
    {
        return $this->makeRequest('put', '/schedules/' . $id, $data);
    }

    public function deleteSchedule($id)
    {
        return $this->makeRequest('delete', '/schedules/' . $id);
    }

    public function searchMovies($query)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/movies/search', ['q' => $query]);

        return $this->handleResponse($response);
    }

    // Review methods
    public function getMovieReviews($movieId)
    {
        return $this->makeRequest('get', '/movies/' . $movieId . '/reviews/public');
    }

    public function createReview($movieId, $reviewData)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/movies/' . $movieId . '/reviews', $reviewData);

        return $this->handleResponse($response);
    }

    // Comment methods
    public function getMovieComments($movieId)
    {
        return $this->makeRequest('get', '/movies/' . $movieId . '/comments/public');
    }

    public function createComment($movieId, $commentData)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/movies/' . $movieId . '/comments', $commentData);

        return $this->handleResponse($response);
    }

    // Admin methods
    public function getUsers()
    {
        // Use public admin endpoint that doesn't require authentication
        return $this->makeRequest('get', '/admin/users');
    }

    public function getUser($userId)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/users/' . $userId);

        return $this->handleResponse($response);
    }

    public function getAllUsers()
    {
        return $this->getUsers();
    }

    public function assignUserRole($userId, $role)
    {
        return $this->makeRequest('post', '/users/' . $userId . '/assign-role', [
            'role' => $role
        ]);
    }

    public function revokeUserAdminRole($userId)
    {
        return $this->makeRequest('post', '/users/' . $userId . '/revoke-admin');
    }

    public function toggleUserStatus($userId)
    {
        return $this->makeRequest('post', '/users/' . $userId . '/toggle-status');
    }

    public function deleteUser($userId)
    {
        return $this->makeRequest('delete', '/users/' . $userId);
    }

    public function exportUsers()
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get($this->baseUrl . '/users/export');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->body(),
                    'content_type' => $response->header('Content-Type'),
                    'filename' => $response->header('Content-Disposition')
                ];
            }

            return [
                'success' => false,
                'message' => 'Không thể xuất dữ liệu người dùng: ' . $response->status()
            ];
        } catch (\Exception $e) {return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gọi API xuất dữ liệu: ' . $e->getMessage()
            ];
        }
    }

    public function getReviews()
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/reviews/public');

        return $this->handleResponse($response);
    }

    public function getAllReviews()
    {
        return $this->getReviews();
    }

    public function getComments()
    {
        return $this->makeRequest('get', '/admin/comments');
    }

    public function getAllComments()
    {
        return $this->getComments();
    }

    public function updateComment($id, $data)
    {
        return $this->makeRequest('put', "/comments/{$id}", $data);
    }

    public function deleteComment($id)
    {
        return $this->makeRequest('delete', "/comments/{$id}");
    }

    public function getViolations()
    {
        return $this->makeRequest('get', '/violations');
    }

    public function reportViolation($data)
    {
        return $this->makeRequest('post', '/violations', $data);
    }

    public function updateViolation($id, $data)
    {
        return $this->makeRequest('put', "/violations/{$id}", $data);
    }

    public function toggleViolationVisibility($id, $data)
    {
        return $this->makeRequest('post', "/admin/violations/{$id}/toggle-visibility", $data);
    }

    // Theater methods
    public function getTheaters()
    {
        return $this->makeRequest('get', '/theaters');
    }

    // Booking methods
    public function getSchedules()
    {
        return $this->makeRequest('get', '/schedules');
    }

    public function getMovieSchedules($movieId)
    {
        return $this->makeRequest('get', '/schedules/movie/' . $movieId);
    }

    public function getMovieSchedulesByTheaterAndDate($movieId, $theaterId, $date)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/theaters/' . $theaterId . '/movies/' . $movieId . '/schedules', [
                'date' => $date
            ]);

        return $this->handleResponse($response);
    }

    // Get current user from session
    public function getCurrentUserFromSession()
    {
        return Session::get('user');
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    // Change password
    public function changePassword($data)
    {
        return $this->makeRequest('post', '/change-password', $data);
    }

    public function getScheduleSeats($scheduleId)
    {
        return $this->makeRequest('get', '/schedules/' . $scheduleId . '/seats');
    }

    public function createBooking($data)
    {
        return $this->makeRequest('post', '/bookings', $data);
    }

    public function getBooking($bookingId)
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get($this->baseUrl . '/bookings/' . $bookingId);
        return $this->handleResponse($response);
    }

    public function getBookingDetails($bookingId)
    {
        return $this->makeRequest('get', '/bookings/' . $bookingId);
    }

    public function lockSeats($data)
    {
        return $this->makeRequest('post', '/bookings/lock-seats', $data);
    }

    public function releaseSeats($data)
    {
        return $this->makeRequest('post', '/bookings/release-seats', $data);
    }

    public function getSnacks()
    {
        return $this->makeRequest('get', '/snacks');
    }

    public function getUserBookings($userId)
    {
        return $this->makeRequest('get', '/users/' . $userId . '/bookings');
    }

    public function cancelBooking($bookingId)
    {
        return $this->makeRequest('post', '/bookings/' . $bookingId . '/cancel');
    }

    // Review methods

    public function updateReview($reviewId, $reviewData)
    {
        return $this->makeRequest('put', '/reviews/' . $reviewId, $reviewData);
    }

    public function deleteReview($reviewId)
    {
        return $this->makeRequest('delete', '/reviews/' . $reviewId);
    }

    // Comment methods

    public function createReply($commentId, $content)
    {
        return $this->makeRequest('post', '/comments/' . $commentId . '/reply', ['content' => $content]);
    }

    // Favorites methods
    public function getUserFavorites($userId)
    {
        return $this->makeRequest('get', '/users/' . $userId . '/favorites');
    }

    public function addToFavorites($movieId, $movieData = null)
    {
        if (!$this->token) {
            return [
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thêm vào yêu thích',
                'error' => 'Unauthenticated'
            ];
        }

        // If movie data is not provided, fetch it
        if (!$movieData) {
            $movieResponse = $this->getMovie($movieId);
            if (!$movieResponse['success']) {
                return [
                    'success' => false,
                    'message' => 'Không thể lấy thông tin phim'
                ];
            }
            $movieData = $movieResponse['data'];
        }

        $favoriteData = [
            'movie_id' => $movieId,
            'title' => $movieData['title'] ?? $movieData['title_vi'] ?? 'Unknown Movie',
            'poster_url' => $movieData['poster'] ?? $movieData['poster_url'] ?? ''
        ];

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseUrl . '/favorites', $favoriteData);
        return $this->handleResponse($response);
    }

    public function removeFromFavorites($movieId)
    {
        if (!$this->token) {
            return [
                'success' => false,
                'message' => 'Bạn cần đăng nhập để bỏ yêu thích',
                'error' => 'Unauthenticated'
            ];
        }

        $response = Http::withHeaders($this->getHeaders())
            ->delete($this->baseUrl . '/favorites/' . $movieId);
        return $this->handleResponse($response);
    }

    // File upload method
    public function uploadFile($request)
    {
        if (!$this->token) {
            return [
                'success' => false,
                'message' => 'No authentication token'
            ];
        }

        try {
            $file = $request->file('file');
            $type = $request->input('type', 'general');

            if (!$file) {
                return [
                    'success' => false,
                    'message' => 'No file provided'
                ];
            }

            // Create multipart request using multipart/form-data
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ])->attach('file', file_get_contents($file->getPathname()), $file->getClientOriginalName())
              ->post($this->baseUrl . '/upload/file', [
                  'type' => $type
              ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ];
        }
    }

    // Update user avatar
    public function updateUserAvatar($avatarUrl)
    {
        return $this->makeRequest('post', '/users/avatar', ['avatar' => $avatarUrl]);
    }

    public function updateUser($userId, $userData)
    {
        return $this->makeRequest('put', '/users/' . $userId, $userData);
    }

    public function createMovie($data)
    {
        // Separate files from regular data
        $files = [];
        $formData = [];
        
        foreach ($data as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $files[$key] = $value;
            } else {
                $formData[$key] = $value;
            }
        }

        // Build multipart data
        $multipart = [];
        foreach ($formData as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $multipart[] = [
                        'name'     => $key . '[]',
                        'contents' => $item
                    ];
                }
            } else {
                // Handle JSON arrays (genre, cast)
                if (in_array($key, ['genre', 'cast']) && is_string($value)) {
                    // Try to decode as JSON first
                    $items = json_decode($value, true);
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            $multipart[] = [
                                'name'     => $key . '[]',
                                'contents' => $item
                            ];
                        }
                    } else {
                        // Fallback to comma-separated
                        if (strpos($value, ',') !== false) {
                            $items = explode(',', $value);
                            foreach ($items as $item) {
                                $multipart[] = [
                                    'name'     => $key . '[]',
                                    'contents' => trim($item)
                                ];
                            }
                        } else {
                            $multipart[] = [
                                'name'     => $key,
                                'contents' => $value
                            ];
                        }
                    }
                } else {
                    $multipart[] = [
                        'name'     => $key,
                        'contents' => $value
                    ];
                }
            }
        }

        foreach ($files as $key => $file) {
            $multipart[] = [
                'name'     => $key,
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
        }

        // Always use with-files endpoint for file uploads
        $endpoint = '/movies/with-files';
        
        $response = Http::withToken($this->token)
            ->asMultipart()
            ->post($this->baseUrl . $endpoint, $multipart);

        $result = $this->handleResponse($response);
        
        return $result;
    }

    public function deleteMovie($id)
    {
        $response = Http::withToken($this->token)
            ->delete($this->baseUrl . '/movies/' . $id);

        return $this->handleResponse($response);
    }

    public function getGenres()
    {
        return $this->makeRequest('get', '/genres');
    }

    public function getDirectors()
    {
        return $this->makeRequest('get', '/directors');
    }

    public function getActors()
    {
        return $this->makeRequest('get', '/actors');
    }

    // Email verification methods
    public function sendOtp($data)
    {
        return $this->makeRequest('post', '/auth/send-otp', $data);
    }

    public function verifyOtp($data)
    {
        $result = $this->makeRequest('post', '/auth/verify-otp', $data);
        
        if ($result['success'] && isset($result['data']['access_token'])) {
            Session::put('jwt_token', $result['data']['access_token']);
            Session::put('user', $result['data']['user']);
            $this->token = $result['data']['access_token'];
        }

        return $result;
    }

    public function forgotPassword($data)
    {
        return $this->makeRequest('post', '/auth/forgot-password', $data);
    }

    public function resetPassword($data)
    {
        return $this->makeRequest('post', '/auth/reset-password', $data);
    }

    // Reports and Statistics methods
    public function getMoviesStats()
    {
        return $this->makeRequest('get', '/admin/statistics/movies');
    }

    public function getUsersStats()
    {
        return $this->makeRequest('get', '/admin/statistics/users');
    }

    public function getReviewsStats()
    {
        return $this->makeRequest('get', '/admin/statistics/reviews');
    }

    public function getBookingsStats()
    {
        return $this->makeRequest('get', '/admin/statistics/bookings');
    }

    public function getMostViewedMovies()
    {
        return $this->makeRequest('get', '/admin/statistics/most-viewed-movies');
    }

    public function getMonthlyRevenue()
    {
        return $this->makeRequest('get', '/admin/statistics/monthly-revenue');
    }

    // Reply methods
    public function createCommentReply($commentId, $data)
    {
        return $this->makeRequest('post', "/comments/{$commentId}/reply", $data);
    }

    public function createReviewReply($reviewId, $data)
    {
        return $this->makeRequest('post', "/reviews/{$reviewId}/reply", $data);
    }

}
