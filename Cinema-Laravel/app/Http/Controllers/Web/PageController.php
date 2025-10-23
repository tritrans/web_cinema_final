<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ApiService;

class PageController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }
    public function about()
    {
        $stats = [
            ['icon' => 'film', 'label' => 'Bộ phim', 'value' => '500+'],
            ['icon' => 'users', 'label' => 'Người dùng', 'value' => '10,000+'],
            ['icon' => 'heart', 'label' => 'Lượt yêu thích', 'value' => '50,000+'],
            ['icon' => 'award', 'label' => 'Giải thưởng', 'value' => '25+'],
        ];

        $features = [
            [
                'title' => 'Bộ sưu tập phong phú',
                'description' => 'Hàng trăm bộ phim Việt Nam từ cổ điển đến hiện đại, từ nghệ thuật đến thương mại.',
            ],
            [
                'title' => 'Chất lượng cao',
                'description' => 'Tất cả phim đều được digitize với chất lượng HD, âm thanh rõ ràng.',
            ],
            [
                'title' => 'Phụ đề đầy đủ',
                'description' => 'Phụ đề tiếng Việt và tiếng Anh cho tất cả các bộ phim.',
            ],
            [
                'title' => 'Cộng đồng yêu phim',
                'description' => 'Kết nối với những người yêu điện ảnh Việt Nam trên khắp thế giới.',
            ],
        ];

        $team = [
            [
                'name' => 'Hứa Minh Hoàng',
                'role' => 'Thành viên nhóm',
                'description' => 'Sinh viên K13 khoa công nghệ thông tin',
                'avatar' => 'https://scontent.fsgn5-15.fna.fbcdn.net/v/t39.30808-6/416352891_3958475811046188_3813556086610444868_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeGDw3BQQpaC2_CHxxtK_zNHEkxC3BbLNAcSTELcFss0B-V2pK0VjSgTZWJcCI-hzQjrqy7gRv_aUYymTGjJPpTz&_nc_ohc=i2oFA-OFHv8Q7kNvwHgGaJa&_nc_oc=AdlnxsWBZupGu7qw5IDg8emnngg7EJQ13UhOt11kXznAP489ersuIe2HfgAY6svXpAU&_nc_zt=23&_nc_ht=scontent.fsgn5-15.fna&_nc_gid=7shFR616z-eFr5n8bVAMQQ&oh=00_AfWJ0cwz54R4d0sIohjEaDS7LBfTJOOyC-jKR6tUaGpDUg&oe=68AB3F5A',
            ],
            [
                'name' => 'Trịnh Đặng Thành Nam',
                'role' => 'Thành viên nhóm',
                'description' => 'Sinh viên K13 khoa công nghệ thông tin',
                'avatar' => 'https://scontent.fsgn5-10.fna.fbcdn.net/v/t39.30808-6/406787321_3553725478217104_3280298165312313883_n.jpg?_nc_cat=106&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeFwZsd_n1Yj7SfCivL5HSpAVr-86cyfcQpWv7zpzJ9xCkNstcV4OCcAxn-JKWBXx9FYqUsUjUX9ClrlC_WVuhDr&_nc_ohc=cvOPo6Vek4kQ7kNvwEs92gY&_nc_oc=AdmFDIw-8iewFh_fPJjxvpXGUfnL2dQKFYYKKajBlJUptMGJnLduLefOeC5w9YrVqFc&_nc_zt=23&_nc_ht=scontent.fsgn5-10.fna&_nc_gid=RaCk3kIxesiRuGs9gDh8Ww&oh=00_AfXsMYFJoIygHd-dDxtJ7lv7_9FUKIxwYO953_N3EQiOLQ&oe=68AB4A52',
            ],
            [
                'name' => 'Trần Minh Trí',
                'role' => 'Thành viên nhóm',
                'description' => 'Sinh viên K13 khoa công nghệ thông tin',
                'avatar' => 'https://i.imgur.com/QGrJxJg.jpeg',
            ],
            [
                'name' => 'Lê Huỳnh Tấn Phước',
                'role' => 'Thành viên nhóm',
                'description' => 'Sinh viên K13 khoa công nghệ thông tin',
                'avatar' => '/images/placeholder-avatar.svg',
            ],
        ];

        return view('about', compact('stats', 'features', 'team'));
    }

    public function infoUser()
    {
        // Check if user is logged in
        if (!session('user')) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }

        try {
            // Get current user data from API
            $userResponse = $this->apiService->getCurrentUser();
            
            if ($userResponse['success']) {
                // Update session with fresh user data
                session(['user' => $userResponse['data']]);
                $user = $userResponse['data'];
            } else {
                // Fallback to session data if API fails
                $user = session('user');
            }

            return view('info-user', compact('user'));
        } catch (\Exception $e) {
            \Log::error('InfoUser error:', [
                'message' => $e->getMessage(),
                'user_id' => session('user.id')
            ]);
            // Fallback to session data
            $user = session('user');
            return view('info-user', compact('user'));
        }
    }

    public function myTickets()
    {
        // Check if user is logged in
        if (!session('user')) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }

        try {
            $user = session('user');
            $userId = $user['id'];
            
            // Get user bookings
            $bookingsResponse = $this->apiService->getUserBookings($userId);
            $bookings = [];
            
            if ($bookingsResponse['success'] && isset($bookingsResponse['data'])) {
                $data = $bookingsResponse['data'];
                
                // Handle different response formats
                if (is_array($data)) {
                    // Check if it's a paginated response
                    if (isset($data['data']) && is_array($data['data'])) {
                        $bookings = $data['data'];
                    } else {
                        // Direct array of bookings
                        $bookings = $data;
                    }
                }
                
                // Ensure all bookings are arrays
                $bookings = array_filter($bookings, function($booking) {
                    return is_array($booking);
                });
            }

            \Log::info('My tickets data:', [
                'user_id' => $userId,
                'bookings_count' => count($bookings),
                'bookings_sample' => count($bookings) > 0 ? $bookings[0] : null
            ]);

            return view('my-tickets', compact('bookings'));
        } catch (\Exception $e) {
            \Log::error('My tickets error:', [
                'message' => $e->getMessage(),
                'user_id' => session('user.id')
            ]);
            return redirect()->route('home')->with('error', 'Không thể tải danh sách vé');
        }
    }

    public function favorites()
    {
        // Check if user is logged in
        if (!session('user')) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để truy cập trang này');
        }

        try {
            $user = session('user');
            $userId = $user['id'];
            
            // Get user favorites
            $favoritesResponse = $this->apiService->getUserFavorites($userId);
            $favorites = [];
            
            if ($favoritesResponse['success'] && isset($favoritesResponse['data']['data'])) {
                $favorites = $favoritesResponse['data']['data'];
            }

            \Log::info('Favorites data:', [
                'user_id' => $userId,
                'favorites_count' => count($favorites),
                'favorites_sample' => count($favorites) > 0 ? $favorites[0] : null
            ]);

            return view('favorites', compact('favorites'));
        } catch (\Exception $e) {
            \Log::error('Favorites error:', [
                'message' => $e->getMessage(),
                'user_id' => session('user.id')
            ]);
            return redirect()->route('home')->with('error', 'Không thể tải danh sách yêu thích');
        }
    }
}
