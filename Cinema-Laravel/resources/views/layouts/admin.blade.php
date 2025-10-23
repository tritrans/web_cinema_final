<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị') - Phim Việt</title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Custom CSS Variables -->
    <style>
        :root {
            --primary: #0ea5e9;
            --primary-foreground: #ffffff;
            --secondary: #f1f5f9;
            --secondary-foreground: #0f172a;
            --muted: #f8fafc;
            --muted-foreground: #64748b;
            --accent: #f1f5f9;
            --accent-foreground: #0f172a;
            --destructive: #ef4444;
            --destructive-foreground: #ffffff;
            --border: #e2e8f0;
            --input: #e2e8f0;
            --ring: #0ea5e9;
            --background: #f8fafc;
            --foreground: #0f172a;
            --card: #ffffff;
            --card-foreground: #0f172a;
            --sidebar: #1e293b;
            --sidebar-foreground: #f1f5f9;
        }
    </style>
    
    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js" 
            onerror="this.onerror=null; this.src='https://unpkg.com/lucide@latest/dist/umd/lucide.js';"></script>
    
    <!-- Custom Styles -->
    <style>
        /* Color Classes */
        .bg-primary { background-color: var(--primary); }
        .text-primary-foreground { color: var(--primary-foreground); }
        .bg-secondary { background-color: var(--secondary); }
        .text-secondary-foreground { color: var(--secondary-foreground); }
        .bg-muted { background-color: var(--muted); }
        .text-muted-foreground { color: var(--muted-foreground); }
        .bg-accent { background-color: var(--accent); }
        .text-accent-foreground { color: var(--accent-foreground); }
        .bg-destructive { background-color: var(--destructive); }
        .text-destructive-foreground { color: var(--destructive-foreground); }
        .border-border { border-color: var(--border); }
        .border-input { border-color: var(--input); }
        .ring-ring { --tw-ring-color: var(--ring); }
        .bg-background { background-color: var(--background); }
        .text-foreground { color: var(--foreground); }
        .bg-card { background-color: var(--card); }
        .text-card-foreground { color: var(--card-foreground); }
        .bg-sidebar { background-color: var(--sidebar); }
        .text-sidebar-foreground { color: var(--sidebar-foreground); }
        
        .sidebar-item {
            display: flex !important;
            align-items: center !important;
            width: 100%;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 2px;
            border-radius: 8px;
            position: relative;
        }
        .sidebar-item:hover {
            @apply bg-gray-50 text-gray-800;
        }
        .sidebar-item.active {
            background-color: #f97316 !important;
            color: white !important;
            font-weight: 500;
            border-radius: 6px;
            margin-left: -16px;
            margin-right: -16px;
            padding-left: 19px;
            padding-right: 19px;
        }
        .sidebar-item.inactive {
            @apply text-gray-700 hover:text-gray-800;
        }
        
        .sidebar-item i {
            transition: all 0.2s ease;
        }
        
        .sidebar-item.active i {
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));
        }
        
        .sidebar-header {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .sidebar-logo {
            background-color: #0d9488;
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
            box-shadow: 0 2px 8px rgba(15, 118, 110, 0.3);
        }
        
        .stat-card {
            @apply bg-white rounded-lg p-4 shadow-sm border border-gray-200;
            transition: all 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            @apply text-2xl font-bold text-gray-900;
        }
        
        .stat-label {
            @apply text-sm text-gray-600 mt-1;
        }
        
        .stat-icon {
            @apply w-8 h-8 rounded flex items-center justify-center;
        }
        
        .sidebar-container {
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
        }
        /* Toggle Switch Styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 44px;
            height: 24px;
            position: absolute;
            z-index: 2;
            cursor: pointer;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #d1d5db;
            transition: .4s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            border: 1px solid #d1d5db;
        }
        
        input:checked + .toggle-slider {
            background-color: #0d9488;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(20px);
            border-color: white;
        }
        
        .toggle-switch:hover .toggle-slider {
            box-shadow: 0 0 8px rgba(13, 148, 136, 0.3);
        }
        
        input:focus + .toggle-slider {
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
        }
        
        /* Custom styles for form inputs to always show border */
        input[type="text"], input[type="date"], input[type="number"], input[type="url"], textarea {
            border: 1px solid #e2e8f0 !important;
        }
    </style>
</head>
<body class="bg-background text-foreground">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="fixed left-0 top-0 h-full w-60 sidebar-container">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="p-4 sidebar-header">
                    <div class="flex items-center space-x-3">
                        <div class="h-8 w-8 rounded sidebar-logo flex items-center justify-center">
                            <span class="text-white font-bold text-lg">P</span>
                        </div>
                        <div>
                            <span class="font-bold text-lg text-gray-900">Phim Việt</span>
                            <p class="text-sm text-gray-500 mt-0.5">Bảng điều khiển quản trị</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1">
                    @php
                        $user = session('user');
                        $currentRoute = request()->route()->getName();
                        $currentUrl = request()->url();
                        
                        // Debug current route
                        // dd($currentRoute, $currentUrl);
                        
                        $menuItems = [
                            [
                                'title' => 'Tổng quan',
                                'route' => 'admin.dashboard',
                                'icon' => 'layout-dashboard',
                                'roles' => ['admin', 'review_manager', 'movie_manager', 'violation_manager']
                            ],
                            [
                                'title' => 'Quản lý phim',
                                'route' => 'admin.movies',
                                'icon' => 'film',
                                'roles' => ['admin', 'movie_manager']
                            ],
                            [
                                'title' => 'Thêm phim mới',
                                'route' => 'admin.movies.new',
                                'icon' => 'plus',
                                'roles' => ['admin', 'movie_manager']
                            ],
                            [
                                'title' => 'Quản lý rạp',
                                'route' => 'admin.theaters',
                                'icon' => 'building-2',
                                'roles' => ['admin', 'movie_manager']
                            ],
                            [
                                'title' => 'Người dùng',
                                'route' => 'admin.users',
                                'icon' => 'users',
                                'roles' => ['admin']
                            ],
                            [
                                'title' => 'Đánh giá',
                                'route' => 'admin.reviews',
                                'icon' => 'star',
                                'roles' => ['admin', 'review_manager', 'violation_manager']
                            ],
                            [
                                'title' => 'Bình luận',
                                'route' => 'admin.comments',
                                'icon' => 'message-square',
                                'roles' => ['admin', 'review_manager', 'violation_manager']
                            ],
                            [
                                'title' => 'Quản lý vi phạm',
                                'route' => 'admin.violations',
                                'icon' => 'alert-triangle',
                                'roles' => ['admin', 'violation_manager']
                            ],
                            [
                                'title' => 'Báo cáo thống kê',
                                'route' => 'admin.reports',
                                'icon' => 'bar-chart-3',
                                'roles' => ['admin', 'review_manager']
                            ]
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                        @if($user && in_array($user['role'], $item['roles']))
                            <a href="{{ route($item['route']) }}" 
                               class="sidebar-item {{ $currentRoute === $item['route'] ? 'active' : 'inactive' }}"
                               style="display: flex; align-items: center;">
                                <i data-lucide="{{ $item['icon'] }}" class="mr-3 h-4 w-4" style="flex-shrink: 0;"></i>
                                <span>{{ $item['title'] }}</span>
                            </a>
                        @endif
                    @endforeach
                </nav>

                <!-- Footer -->
                <div class="p-3 border-t border-gray-200 space-y-3">
                    <a href="{{ route('home') }}" class="sidebar-item inactive" style="display: flex; align-items: center;">
                        <i data-lucide="home" class="mr-3 h-4 w-4" style="flex-shrink: 0;"></i>
                        <span>Về trang chủ</span>
                    </a>

                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        @if(isset($user['avatar']) && $user['avatar'])
                            @php
                                $avatarUrl = $user['avatar'];
                                // If it's a relative path, prepend the web app URL
                                if (strpos($avatarUrl, 'http') !== 0) {
                                    $avatarUrl = url('storage/' . $avatarUrl);
                                }
                            @endphp
                            <div class="h-10 w-10 rounded-full overflow-hidden">
                                <img src="{{ $avatarUrl }}" 
                                     alt="Avatar" 
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="h-10 w-10 rounded-full user-avatar flex items-center justify-center" style="display: none;">
                                    <span class="text-white text-sm font-medium">
                                        {{ strtoupper(substr($user['name'] ?? 'T', 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-full user-avatar flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    {{ strtoupper(substr($user['name'] ?? 'T', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $user['name'] ?? 'tran minh tri' }}</p>
                            <p class="text-xs text-gray-500">Quản trị viên</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="sidebar-item inactive w-full text-left" style="display: flex; align-items: center;">
                            <i data-lucide="log-out" class="mr-3 h-4 w-4" style="flex-shrink: 0;"></i>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 ml-60 bg-gray-50 min-h-screen">
            <div>
                <!-- Breadcrumb -->
                @if(isset($breadcrumb))
                    <nav class="flex mb-6" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-muted-foreground hover:text-primary">
                                    <i data-lucide="home" class="w-4 h-4 mr-2"></i>
                                    Tổng quan
                                </a>
                            </li>
                            @foreach($breadcrumb as $item)
                                <li>
                                    <div class="flex items-center">
                                        <i data-lucide="chevron-right" class="w-4 h-4 text-muted-foreground"></i>
                                        @if(isset($item['url']))
                                            <a href="{{ $item['url'] }}" class="ml-1 text-sm font-medium text-muted-foreground hover:text-primary md:ml-2">
                                                {{ $item['title'] }}
                                            </a>
                                        @else
                                            <span class="ml-1 text-sm font-medium text-foreground md:ml-2">
                                                {{ $item['title'] }}
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                @endif

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="check-circle" class="h-5 w-5 text-green-600 mr-2"></i>
                            <span class="text-green-800">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="h-5 w-5 text-red-600 mr-2"></i>
                            <span class="text-red-800">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center">
                            <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-600 mr-2"></i>
                            <span class="text-yellow-800">{{ session('warning') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Check if lucide is loaded
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
                console.log('Lucide icons initialized successfully');
            } else {
                console.error('Lucide library failed to load');
                // Fallback: show text instead of icons
                document.querySelectorAll('[data-lucide]').forEach(function(element) {
                    const iconName = element.getAttribute('data-lucide');
                    element.innerHTML = iconName.charAt(0).toUpperCase() + iconName.slice(1).replace(/-/g, ' ');
                    element.style.fontSize = '12px';
                    element.style.fontWeight = 'bold';
                });
            }
        });
    </script>

    <!-- Custom Scripts -->
    @stack('scripts')
</body>
</html>
