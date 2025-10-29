@extends('layouts.admin')

@section('title', 'Bảng điều khiển')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bảng điều khiển</h1>
            <p class="text-gray-600 mt-1">Tổng quan về hệ thống quản lý phim - Dữ liệu thời gian thực</p>
        </div>
        <button onclick="window.location.reload()" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 rounded-lg text-sm font-medium text-gray-700 shadow-sm">
            <i data-lucide="refresh-cw" class="h-4 w-4"></i>
            Làm mới
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $statsCards = [];
            
            // Super Admin và Admin: Hiển thị tất cả
            if (in_array($user['role'], ['super_admin', 'admin'])) {
                $statsCards = [
                    [
                        'title' => 'Tổng số phim',
                        'value' => $stats['movies']['total'] ?? 0,
                        'subtitle' => ($stats['movies']['featured'] ?? 0) . ' phim nổi bật',
                        'icon' => 'film',
                        'bg_color' => 'bg-blue-100',
                        'icon_color' => 'text-blue-600'
                    ],
                        [
                            'title' => 'Người dùng',
                            'value' => $stats['users']['total'] ?? 0,
                            'subtitle' => ($stats['users']['today'] ?? 0) . ' đăng ký hôm nay',
                            'icon' => 'users',
                            'bg_color' => 'bg-green-100',
                            'icon_color' => 'text-green-600'
                        ],
                    [
                        'title' => 'Đánh giá',
                        'value' => $stats['reviews']['total'] ?? 0,
                        'subtitle' => 'Trung bình ' . number_format($stats['reviews']['average_rating'] ?? 0, 1) . '/5 sao',
                        'icon' => 'star',
                        'bg_color' => 'bg-yellow-100',
                        'icon_color' => 'text-yellow-600'
                    ],
                    [
                        'title' => 'Bình luận',
                        'value' => $stats['comments']['total'] ?? 0,
                        'subtitle' => ($stats['comments']['today'] ?? 0) . ' hôm nay',
                        'icon' => 'message-square',
                        'bg_color' => 'bg-purple-100',
                        'icon_color' => 'text-purple-600'
                    ]
                ];
            }
            // Movie Manager: Chỉ hiển thị phim và rạp
            elseif ($user['role'] === 'movie_manager') {
                $statsCards = [
                    [
                        'title' => 'Tổng số phim',
                        'value' => $stats['movies']['total'] ?? 0,
                        'subtitle' => ($stats['movies']['featured'] ?? 0) . ' phim nổi bật',
                        'icon' => 'film',
                        'color' => 'text-blue-600'
                    ],
                    [
                        'title' => 'Rạp chiếu',
                        'value' => $stats['theaters']['total'] ?? 0,
                        'subtitle' => ($stats['theaters']['active'] ?? 0) . ' đang hoạt động',
                        'icon' => 'building-2',
                        'color' => 'text-green-600'
                    ]
                ];
            }
            // Review Manager: Chỉ hiển thị đánh giá và bình luận
            elseif ($user['role'] === 'review_manager') {
                $statsCards = [
                    [
                        'title' => 'Đánh giá',
                        'value' => $stats['reviews']['total'] ?? 0,
                        'subtitle' => 'Trung bình ' . number_format($stats['reviews']['average_rating'] ?? 0, 1) . '/5 sao',
                        'icon' => 'star',
                        'color' => 'text-yellow-600'
                    ],
                    [
                        'title' => 'Bình luận',
                        'value' => $stats['comments']['total'] ?? 0,
                        'subtitle' => ($stats['comments']['today'] ?? 0) . ' hôm nay',
                        'icon' => 'message-square',
                        'color' => 'text-green-600'
                    ]
                ];
            }
            // Violation Manager: Hiển thị đánh giá và bình luận để kiểm tra
            elseif ($user['role'] === 'violation_manager') {
                $statsCards = [
                    [
                        'title' => 'Đánh giá',
                        'value' => $stats['reviews']['total'] ?? 0,
                        'subtitle' => 'Cần kiểm tra vi phạm',
                        'icon' => 'star',
                        'color' => 'text-yellow-600'
                    ],
                    [
                        'title' => 'Bình luận',
                        'value' => $stats['comments']['total'] ?? 0,
                        'subtitle' => 'Cần kiểm tra vi phạm',
                        'icon' => 'message-square',
                        'color' => 'text-green-600'
                    ]
                ];
            }
        @endphp

        @foreach($statsCards as $card)
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded flex items-center justify-center {{ $card['bg_color'] ?? 'bg-blue-100' }}">
                            <i data-lucide="{{ $card['icon'] }}" class="h-4 w-4 {{ $card['icon_color'] ?? 'text-blue-600' }}"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ $card['title'] }}</span>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900 mb-1">{{ number_format($card['value']) }}</div>
                @if(isset($card['subtitle']))
                    <div class="text-xs text-gray-500">{{ $card['subtitle'] }}</div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(in_array($user['role'], ['super_admin', 'admin', 'movie_manager']))
            <!-- Recent Movies -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Phim mới nhất</h3>
                    <div class="space-y-4">
                        @if(isset($stats['movies']['recent']) && count($stats['movies']['recent']) > 0)
                            @foreach($stats['movies']['recent'] as $movie)
                                <div class="flex items-center justify-between py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 shadow-sm">
                                            @if(isset($movie['poster']) || isset($movie['poster_url']))
                                                <img src="{{ \App\Helpers\ImageHelper::getMoviePoster($movie) }}" 
                                                     alt="{{ $movie['title'] ?? 'Movie Poster' }}" 
                                                     class="w-full h-full object-cover"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                     onload="console.log('Dashboard poster loaded:', this.src);"
                                                     loading="lazy">
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center" style="display: none;">
                                                    <i data-lucide="film" class="h-6 w-6 text-gray-400"></i>
                                                </div>
                                            @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <i data-lucide="film" class="h-6 w-6 text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 text-sm truncate">{{ $movie['title'] ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ isset($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y') : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs rounded font-medium">{{ $movie['genre'] ?? 'N/A' }}</span>
                                        <div class="flex items-center space-x-1">
                                            <i data-lucide="star" class="h-3 w-3 text-yellow-500 fill-current"></i>
                                            <span class="text-xs text-gray-700 font-medium">{{ number_format($movie['rating'] ?? 0, 1) }}/5</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i data-lucide="film" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                                <p class="text-sm">Chưa có phim nào</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

                @if(in_array($user['role'], ['super_admin', 'admin']))
                    <!-- Recent Users -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Người dùng mới (Hôm nay)</h3>
                            <div class="space-y-4">
                                @if(isset($stats['users']['recent']) && count($stats['users']['recent']) > 0)
                                    @foreach($stats['users']['recent'] as $recentUser)
                                        <div class="flex items-center space-x-4">
                                            <div class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                @if($recentUser['avatar'])
                                                    <img src="{{ $recentUser['avatar'] }}" alt="{{ $recentUser['name'] }}" class="h-8 w-8 rounded-full">
                                                @else
                                                    <span class="text-xs font-medium text-gray-600">{{ strtoupper(substr($recentUser['name'], 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $recentUser['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $recentUser['email'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-medium">Mới</span>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($recentUser['created_at'])->locale('vi')->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <i data-lucide="user-plus" class="h-8 w-8 mx-auto mb-2 text-gray-400"></i>
                                        <p class="text-sm">Chưa có người dùng mới hôm nay</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

        @if(in_array($user['role'], ['review_manager', 'violation_manager']))
            <!-- Recent Reviews -->
            <div class="bg-card border border-border rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ $user['role'] === 'violation_manager' ? 'Đánh giá cần kiểm tra' : 'Đánh giá mới nhất' }}
                    </h3>
                    <div class="space-y-4" id="latest-reviews">
                        @if(isset($stats['reviews']['latest']) && count($stats['reviews']['latest']) > 0)
                            @foreach($stats['reviews']['latest'] as $review)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        @if(isset($review['user_avatar_url']) && $review['user_avatar_url'])
                                            <img src="{{ $review['user_avatar_url'] }}" alt="Avatar" class="w-8 h-8 rounded-full">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $review['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                        <div class="flex items-center space-x-1 mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= ($review['rating'] ?? 0))
                                                    <i data-lucide="star" class="h-3 w-3 text-yellow-400 fill-current"></i>
                                                @else
                                                    <i data-lucide="star" class="h-3 w-3 text-gray-300"></i>
                                                @endif
                                            @endfor
                                            <span class="text-xs text-gray-500 ml-1">{{ $review['rating'] ?? 0 }}/5</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($review['comment'] ?? '', 100) }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ isset($review['created_at']) ? \Carbon\Carbon::parse($review['created_at'])->diffForHumans() : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 text-muted-foreground">
                                <p>Chưa có dữ liệu đánh giá</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="bg-card border border-border rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        {{ $user['role'] === 'violation_manager' ? 'Bình luận cần kiểm tra' : 'Bình luận mới nhất' }}
                    </h3>
                    <div class="space-y-4" id="latest-comments">
                        @if(isset($stats['comments']['latest']) && count($stats['comments']['latest']) > 0)
                            @foreach($stats['comments']['latest'] as $comment)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        @if(isset($comment['user_avatar_url']) && $comment['user_avatar_url'])
                                            <img src="{{ $comment['user_avatar_url'] }}" alt="Avatar" class="w-8 h-8 rounded-full">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                                                <span class="text-white text-sm font-medium">
                                                    {{ strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $comment['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($comment['content'] ?? '', 100) }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ isset($comment['created_at']) ? \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 text-muted-foreground">
                                <p>Chưa có dữ liệu bình luận</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $quickActions = [];
                    
                    // Super Admin và Admin có quyền truy cập tất cả
                    if (in_array($user['role'], ['super_admin', 'admin'])) {
                        $quickActions = [
                            ['href' => route('admin.movies'), 'label' => 'Quản lý phim', 'description' => 'Xem và chỉnh sửa phim', 'icon' => 'film', 'color' => 'primary'],
                            ['href' => route('admin.users'), 'label' => 'Quản lý người dùng', 'description' => 'Xem và chỉnh sửa', 'icon' => 'users', 'color' => 'secondary'],
                            ['href' => route('admin.reports'), 'label' => 'Xem báo cáo', 'description' => 'Thống kê chi tiết', 'icon' => 'bar-chart-3', 'color' => 'accent']
                        ];
                    }
                    // Review Manager
                    elseif ($user['role'] === 'review_manager') {
                        $quickActions = [
                            ['href' => route('admin.reviews'), 'label' => 'Quản lý đánh giá', 'description' => 'Xem và quản lý đánh giá', 'icon' => 'star', 'color' => 'accent'],
                            ['href' => route('admin.comments'), 'label' => 'Quản lý bình luận', 'description' => 'Xem và quản lý bình luận', 'icon' => 'message-square', 'color' => 'secondary'],
                            ['href' => route('admin.reports'), 'label' => 'Báo cáo thống kê', 'description' => 'Xem thống kê và báo cáo', 'icon' => 'bar-chart-3', 'color' => 'primary']
                        ];
                    }
                    // Movie Manager
                    elseif ($user['role'] === 'movie_manager') {
                        $quickActions = [
                            ['href' => route('admin.movies'), 'label' => 'Quản lý phim', 'description' => 'Xem và chỉnh sửa phim', 'icon' => 'film', 'color' => 'primary'],
                            ['href' => route('admin.theaters'), 'label' => 'Quản lý rạp', 'description' => 'Xem và quản lý rạp chiếu', 'icon' => 'building-2', 'color' => 'secondary']
                        ];
                    }
                    // Violation Manager
                    elseif ($user['role'] === 'violation_manager') {
                        $quickActions = [
                            ['href' => route('admin.violations'), 'label' => 'Quản lý vi phạm', 'description' => 'Xem và xử lý báo cáo', 'icon' => 'alert-triangle', 'color' => 'destructive'],
                            ['href' => route('admin.reviews'), 'label' => 'Quản lý đánh giá', 'description' => 'Kiểm tra đánh giá', 'icon' => 'star', 'color' => 'accent'],
                            ['href' => route('admin.comments'), 'label' => 'Quản lý bình luận', 'description' => 'Kiểm tra bình luận', 'icon' => 'message-square', 'color' => 'secondary']
                        ];
                    }
                @endphp

                @foreach($quickActions as $action)
                    <a href="{{ $action['href'] }}" class="block">
                        <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i data-lucide="{{ $action['icon'] }}" class="h-5 w-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $action['label'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $action['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection