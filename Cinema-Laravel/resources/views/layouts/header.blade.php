<header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
    <div class="container mx-auto flex h-14 sm:h-16 items-center justify-between px-3 sm:px-4 lg:px-6">
        <!-- Logo -->
        <a href="{{ session('user') ? route('home') : route('home') }}" class="flex items-center space-x-2 flex-shrink-0">
            <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-lg bg-primary flex items-center justify-center">
                <span class="text-primary-foreground font-bold text-lg sm:text-xl">P</span>
            </div>
            <span class="font-bold text-lg sm:text-xl text-primary xs:block">Phim Việt</span>
        </a>

        <nav class="hidden lg:flex items-center space-x-6 xl:space-x-8">
            <a href="{{ session('user') ? route('home') : route('home') }}" class="text-foreground hover:text-primary transition-colors font-medium px-2 py-1">
                Trang chủ
            </a>
            <a href="{{ route('movies.index') }}" class="text-muted-foreground hover:text-primary transition-colors font-medium px-2 py-1">
                Phim
            </a>
            <a href="/about" class="text-muted-foreground hover:text-primary transition-colors font-medium px-2 py-1">
                Giới thiệu
            </a>
        </nav>

        <div class="flex items-center space-x-1 sm:space-x-2 lg:space-x-4">
            <!-- User Actions -->
            @if(session('user'))
                <div class="relative group">
                    <button class="relative h-9 w-9 rounded-full flex items-center justify-center hover:bg-accent transition-colors" onclick="toggleUserMenu()">
                        <div class="avatar h-8 w-8">
                            @php
                                $avatarUrl = session('user.avatar');
                                if ($avatarUrl && strpos($avatarUrl, 'http') !== 0) {
                                    $avatarUrl = url('storage/' . $avatarUrl);
                                }
                                // Add timestamp to prevent caching
                                if ($avatarUrl) {
                                    $avatarUrl .= '?t=' . time();
                                }
                            @endphp
                            <img src="{{ $avatarUrl ?: '/placeholder.svg' }}" 
                                 alt="{{ session('user.name') ?: session('user.email') }}" 
                                 class="avatar-image rounded-full"
                                 id="header-avatar"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="avatar-fallback text-sm" style="display: none;">
                                {{ session('user.name') ? strtoupper(substr(session('user.name'), 0, 1)) : (session('user.email') ? strtoupper(substr(session('user.email'), 0, 1)) : 'U') }}
                            </div>
                        </div>
                    </button>
                    
                    <div id="user-menu" class="absolute right-0 mt-2 w-56 bg-popover border rounded-md shadow-lg opacity-0 invisible transition-all duration-200 z-50">
                        <div class="p-2">
                            <div class="flex items-center justify-start gap-2 p-2">
                                <div class="flex flex-col space-y-1 leading-none">
                                    <p class="font-medium">{{ session('user.name') ?: session('user.email') }}</p>
                                    <p class="w-[200px] truncate text-sm text-muted-foreground">{{ session('user.email') }}</p>
                                </div>
                            </div>
                            <hr class="my-1">
                            <a href="/info-user" class="flex items-center w-full text-left px-2 py-2 hover:bg-accent rounded-sm">
                                <i data-lucide="user" class="mr-2 h-4 w-4"></i>
                                <span>Hồ sơ</span>
                            </a>
                            <a href="/my-tickets" class="flex items-center w-full text-left px-2 py-2 hover:bg-accent rounded-sm">
                                <i data-lucide="ticket" class="mr-2 h-4 w-4"></i>
                                <span>Vé của tôi</span>
                            </a>
                            <a href="{{ route('favorites') }}" class="flex items-center w-full text-left px-2 py-2 hover:bg-accent rounded-sm">
                                <i data-lucide="heart" class="mr-2 h-4 w-4"></i>
                                <span>Yêu thích</span>
                            </a>
                            <button onclick="handleSettings()" class="flex items-center w-full text-left px-2 py-2 hover:bg-accent rounded-sm">
                                <i data-lucide="settings" class="mr-2 h-4 w-4"></i>
                                <span>Cài đặt</span>
                            </button>
                            
                            @if(in_array(session('user.role'), ['admin', 'super_admin', 'review_manager', 'movie_manager', 'violation_manager']))
                                <hr class="my-1">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center w-full text-left px-2 py-2 hover:bg-accent rounded-sm">
                                    <i data-lucide="shield" class="mr-2 h-4 w-4"></i>
                                    <span>Quản trị</span>
                                </a>
                            @endif
                            
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="flex items-center w-full text-left px-2 py-2 hover:bg-accent rounded-sm">
                                    <i data-lucide="log-out" class="mr-2 h-4 w-4"></i>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="hidden sm:flex items-center space-x-2">
                    <a href="{{ route('login') }}" class="text-muted-foreground hover:text-primary transition-colors font-medium px-2 py-1">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center h-8 px-3 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 transition-colors">
                        Đăng ký
                    </a>
                </div>
            @endif

            <!-- Mobile menu button -->
            <button class="lg:hidden h-9 w-9 rounded-full flex items-center justify-center hover:bg-accent transition-colors" onclick="toggleMobileMenu()">
                <i data-lucide="menu" class="h-5 w-5"></i>
                <span class="sr-only">Menu</span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="lg:hidden fixed inset-0 z-50 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 opacity-0 invisible transition-all duration-200">
        <div class="w-[280px] sm:w-[320px] h-full bg-background border-l shadow-lg ml-auto">
            <div class="p-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold">Menu</h2>
                    <button onclick="toggleMobileMenu()" class="h-9 w-9 rounded-full flex items-center justify-center hover:bg-accent transition-colors">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <div class="flex flex-col space-y-6">
                    <div class="sm:hidden">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
                            <input type="search" placeholder="Tìm kiếm phim..." class="input w-full pl-10 h-11">
                        </div>
                    </div>

                    <nav class="flex flex-col space-y-1">
                        <a href="{{ session('user') ? route('home') : route('home') }}" 
                           class="flex items-center space-x-3 text-foreground hover:text-primary hover:bg-accent transition-colors font-medium py-3 px-3 rounded-md"
                           onclick="toggleMobileMenu()">
                            <i data-lucide="home" class="h-5 w-5"></i>
                            <span>Trang chủ</span>
                        </a>
                        <a href="{{ route('movies.index') }}" 
                           class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors font-medium py-3 px-3 rounded-md"
                           onclick="toggleMobileMenu()">
                            <i data-lucide="film" class="h-5 w-5"></i>
                            <span>Phim</span>
                        </a>
                        <a href="{{ route('movies.genres') }}" 
                           class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors font-medium py-3 px-3 rounded-md"
                           onclick="toggleMobileMenu()">
                            <i data-lucide="grid-3x3" class="h-5 w-5"></i>
                            <span>Thể loại</span>
                        </a>
                        <a href="/about" 
                           class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors font-medium py-3 px-3 rounded-md"
                           onclick="toggleMobileMenu()">
                            <i data-lucide="info" class="h-5 w-5"></i>
                            <span>Giới thiệu</span>
                        </a>
                    </nav>

                    @if(!session('user'))
                        <div class="flex flex-col space-y-3 pt-4 border-t">
                            <a href="{{ route('login') }}" 
                               class="btn-outline btn-lg w-full h-11 bg-transparent"
                               onclick="toggleMobileMenu()">
                                <i data-lucide="user" class="mr-2 h-4 w-4"></i>
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}" 
                               class="btn-primary btn-lg w-full h-11"
                               onclick="toggleMobileMenu()">
                                Đăng ký
                            </a>
                        </div>
                    @endif

                    @if(session('user'))
                        <div class="pt-4 border-t">
                            <div class="flex items-center space-x-3 p-3 bg-accent/50 rounded-lg mb-4">
                                <div class="avatar h-10 w-10">
                                    @php
                                        $mobileAvatarUrl = session('user.avatar');
                                        if ($mobileAvatarUrl && strpos($mobileAvatarUrl, 'http') !== 0) {
                                            $mobileAvatarUrl = url('storage/' . $mobileAvatarUrl);
                                        }
                                        // Add timestamp to prevent caching
                                        if ($mobileAvatarUrl) {
                                            $mobileAvatarUrl .= '?t=' . time();
                                        }
                                    @endphp
                                    <img src="{{ $mobileAvatarUrl ?: '/placeholder.svg' }}" 
                                         alt="{{ session('user.name') ?: session('user.email') }}" 
                                         class="avatar-image rounded-full"
                                         id="mobile-avatar"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="avatar-fallback" style="display: none;">
                                        {{ session('user.name') ? strtoupper(substr(session('user.name'), 0, 1)) : (session('user.email') ? strtoupper(substr(session('user.email'), 0, 1)) : 'U') }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate">{{ session('user.name') ?: session('user.email') }}</p>
                                    <p class="text-xs text-muted-foreground truncate">{{ session('user.email') }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col space-y-1">
                                <a href="/info-user" 
                                   onclick="toggleMobileMenu()"
                                   class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors py-2 px-3 rounded-md w-full text-left">
                                    <i data-lucide="user" class="h-4 w-4"></i>
                                    <span>Hồ sơ</span>
                                </a>
                                <a href="/my-tickets" 
                                   onclick="toggleMobileMenu()"
                                   class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors py-2 px-3 rounded-md w-full text-left">
                                    <i data-lucide="ticket" class="h-4 w-4"></i>
                                    <span>Vé của tôi</span>
                                </a>
                                <a href="{{ route('favorites') }}" 
                                   onclick="toggleMobileMenu()"
                                   class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors py-2 px-3 rounded-md w-full text-left">
                                    <i data-lucide="heart" class="h-4 w-4"></i>
                                    <span>Yêu thích</span>
                                </a>
                                <button class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors py-2 px-3 rounded-md w-full text-left"
                                        onclick="handleSettings(); toggleMobileMenu();">
                                    <i data-lucide="settings" class="h-4 w-4"></i>
                                    <span>Cài đặt</span>
                                </button>
                                @if(in_array(session('user.role'), ['admin', 'super_admin', 'review_manager', 'movie_manager', 'violation_manager']))
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors py-2 px-3 rounded-md"
                                       onclick="toggleMobileMenu()">
                                        <i data-lucide="shield" class="h-4 w-4"></i>
                                        <span>Quản trị</span>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center space-x-3 text-muted-foreground hover:text-primary hover:bg-accent transition-colors py-2 px-3 rounded-md w-full text-left"
                                            onclick="toggleMobileMenu()">
                                        <i data-lucide="log-out" class="h-4 w-4"></i>
                                        <span>Đăng xuất</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.avatar {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #3b82f6;
    color: white;
    font-weight: 600;
    border-radius: 50%;
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.avatar-fallback {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #3b82f6;
    color: white;
    font-weight: 600;
    border-radius: 50%;
}

/* Hide fallback when image loads successfully */
.avatar-image:not([src=""]) + .avatar-fallback {
    display: none;
}

/* Show fallback when image fails to load */
.avatar-image[src=""] + .avatar-fallback,
.avatar-image:not([src]) + .avatar-fallback {
    display: flex;
}
</style>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    if (menu.classList.contains('opacity-0')) {
        menu.classList.remove('opacity-0', 'invisible');
        menu.classList.add('opacity-100', 'visible');
    } else {
        menu.classList.remove('opacity-100', 'visible');
        menu.classList.add('opacity-0', 'invisible');
    }
}

function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    if (menu.classList.contains('opacity-0')) {
        menu.classList.remove('opacity-0', 'invisible');
        menu.classList.add('opacity-100', 'visible');
    } else {
        menu.classList.remove('opacity-100', 'visible');
        menu.classList.add('opacity-0', 'invisible');
    }
}


function handleSettings() {
    @if(!session('user'))
        alert('Bạn cần đăng nhập để truy cập cài đặt');
        return;
    @endif
    window.location.href = '/info-user';
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('user-menu');
    const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
    
    if (userMenu && !userButton && !userMenu.contains(event.target)) {
        userMenu.classList.remove('opacity-100', 'visible');
        userMenu.classList.add('opacity-0', 'invisible');
    }
});
</script>
