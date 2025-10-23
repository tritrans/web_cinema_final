@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân - Phim Việt')
@section('description', 'Quản lý thông tin tài khoản và cài đặt cá nhân')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <main class="container mx-auto flex-1 p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 mb-2">Hồ sơ cá nhân</h1>
                <p class="text-slate-600 dark:text-slate-400">Quản lý thông tin tài khoản và cài đặt cá nhân</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="overflow-hidden border-0 shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-lg">
                        <div class="p-8">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative group">
                                    <div class="h-32 w-32 border-4 border-white shadow-xl rounded-full overflow-hidden">
                                        @if(session('user.avatar'))
                                            @php
                                                $avatarUrl = session('user.avatar');
                                                // If it's a relative path, prepend the web app URL
                                                if (strpos($avatarUrl, 'http') !== 0) {
                                                    $avatarUrl = url('storage/' . $avatarUrl);
                                                }
                                            @endphp
                                            <img src="{{ $avatarUrl }}" 
                                                 alt="{{ session('user.name') ?: session('user.email') }}" 
                                                 class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @endif
                                        <div class="w-full h-full flex items-center justify-center text-3xl font-bold bg-gradient-to-br from-blue-500 to-purple-600 text-white {{ session('user.avatar') ? 'hidden' : '' }}">
                                            {{ session('user.name') ? strtoupper(substr(session('user.name'), 0, 1)) : (session('user.email') ? strtoupper(substr(session('user.email'), 0, 1)) : 'U') }}
                                        </div>
                                    </div>
                                    <label class="absolute -bottom-2 -right-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full p-3 cursor-pointer hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-lg group-hover:scale-110">
                                        <i data-lucide="camera" class="h-5 w-5"></i>
                                        <input type="file" accept="image/*" class="hidden" id="avatar-upload">
                                    </label>
                                </div>
                                
                                <div class="mt-6 space-y-2">
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                                        {{ session('user.name') ?: 'Chưa cập nhật' }}
                                    </h3>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 break-all">
                                        {{ session('user.email') }}
                                    </p>
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                        {{ session('user.role') === 'admin' ? 'Quản trị viên' : 'Người dùng' }}
                                    </div>
                                </div>

                                <div class="mt-6 w-full space-y-3">
                                    <a href="{{ route('home') }}" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white shadow-lg inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md font-medium transition-all">
                                        <i data-lucide="home" class="h-4 w-4"></i>
                                        <span>Trang chủ</span>
                                    </a>
                                    <a href="{{ route('movies.index') }}" class="w-full border border-slate-300 hover:bg-slate-50 dark:border-slate-600 dark:hover:bg-slate-700 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md font-medium transition-all">
                                        <i data-lucide="film" class="h-4 w-4"></i>
                                        <span>Xem phim</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Account Information -->
                    <div class="border-0 shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between pb-4">
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                                        Thông tin tài khoản
                                    </h2>
                                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                        Quản lý thông tin cá nhân và bảo mật
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button onclick="openChangePasswordModal()" class="border border-slate-300 hover:bg-slate-50 dark:border-slate-600 dark:hover:bg-slate-700 inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                        <i data-lucide="lock" class="h-4 w-4"></i>
                                        Đổi mật khẩu
                                    </button>
                                    <a href="{{ route('logout') }}" class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-all">
                                        <i data-lucide="log-out" class="h-4 w-4"></i>
                                        Đăng xuất
                                    </a>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Họ và tên</p>
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ session('user.name') ?: 'Chưa cập nhật' }}
                                        </p>
                                    </div>

                                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Email</p>
                                        <p class="font-semibold text-slate-900 dark:text-slate-100 break-all">
                                            {{ session('user.email') }}
                                        </p>
                                    </div>

                                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Vai trò</p>
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ session('user.role') === 'admin' ? 'Quản trị viên' : 'Người dùng' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Thời điểm tạo</p>
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ session('user.created_at') ? \Carbon\Carbon::parse(session('user.created_at'))->diffForHumans() : '-' }}
                                        </p>
                                    </div>

                                    <div class="p-4 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Cập nhật lần cuối</p>
                                        <p class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ session('user.updated_at') ? \Carbon\Carbon::parse(session('user.updated_at'))->diffForHumans() : '-' }}
                                        </p>
                                    </div>

                                    <div class="p-4 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800">
                                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400 mb-1">Trạng thái tài khoản</p>
                                        <p class="font-semibold text-blue-800 dark:text-blue-200">
                                            Hoạt động bình thường
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Section -->
                    <div class="border-0 shadow-lg bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-lg">
                        <div class="p-6">
                            <div class="pb-4">
                                <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                                    Hoạt động gần đây
                                </h2>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Lịch sử các hoạt động của bạn trên hệ thống
                                </p>
                            </div>
                            
                            <div class="text-center py-12">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                    <i data-lucide="film" class="h-8 w-8 text-slate-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
                                    Chưa có hoạt động
                                </h3>
                                <p class="text-slate-600 dark:text-slate-400 mb-6">
                                    Bắt đầu khám phá phim và đặt vé để xem hoạt động của bạn tại đây
                                </p>
                                <a href="{{ route('movies.index') }}" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 inline-flex items-center gap-2 px-4 py-2 rounded-md text-white font-medium transition-all">
                                    <i data-lucide="film" class="h-4 w-4"></i>
                                    Khám phá phim
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Change Password Modal -->
<div id="change-password-modal" class="fixed inset-0 z-50 bg-black/50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Đổi mật khẩu</h3>
                    <button onclick="closeChangePasswordModal()" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Nhập mật khẩu hiện tại và mật khẩu mới để bảo mật tài khoản
                </p>
                
                <form id="change-password-form" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Mật khẩu hiện tại</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" 
                                   class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white pr-10"
                                   placeholder="Nhập mật khẩu hiện tại">
                            <button type="button" onclick="togglePassword('current_password')" 
                                    class="absolute right-0 top-0 h-full px-3 py-2 text-slate-400 hover:text-slate-600">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Mật khẩu mới</label>
                        <div class="relative">
                            <input type="password" name="password" id="new_password" 
                                   class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white pr-10"
                                   placeholder="Nhập mật khẩu mới">
                            <button type="button" onclick="togglePassword('new_password')" 
                                    class="absolute right-0 top-0 h-full px-3 py-2 text-slate-400 hover:text-slate-600">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Xác nhận mật khẩu mới</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="confirm_password" 
                                   class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white pr-10"
                                   placeholder="Nhập lại mật khẩu mới">
                            <button type="button" onclick="togglePassword('confirm_password')" 
                                    class="absolute right-0 top-0 h-full px-3 py-2 text-slate-400 hover:text-slate-600">
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeChangePasswordModal()" 
                                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-md text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            Hủy
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-4 py-2 rounded-md font-medium transition-all">
                            Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openChangePasswordModal() {
    document.getElementById('change-password-modal').classList.remove('hidden');
}

function closeChangePasswordModal() {
    document.getElementById('change-password-modal').classList.add('hidden');
    document.getElementById('change-password-form').reset();
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        field.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    
    // Re-initialize Lucide icons
    lucide.createIcons();
}

// Handle change password form submission
document.getElementById('change-password-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Show loading state
    submitButton.textContent = 'Đang xử lý...';
    submitButton.disabled = true;
    
    try {
        const response = await fetch('{{ route("change-password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                current_password: formData.get('current_password'),
                password: formData.get('password'),
                password_confirmation: formData.get('password_confirmation')
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Đổi mật khẩu thành công!');
            closeChangePasswordModal();
        } else {
            alert(result.message || 'Đổi mật khẩu thất bại');
        }
    } catch (error) {
        alert('Có lỗi xảy ra khi đổi mật khẩu');
    } finally {
        // Reset button state
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }
});

// Helper function to get cookie value
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Handle avatar upload
document.getElementById('avatar-upload').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Vui lòng chọn file ảnh');
        return;
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Kích thước file không được vượt quá 5MB');
        return;
    }
    
    const formData = new FormData();
    formData.append('avatar', file);
    
    // Show loading state
    const uploadButton = this.closest('label');
    const originalContent = uploadButton.innerHTML;
    uploadButton.innerHTML = '<i data-lucide="loader-2" class="h-5 w-5 animate-spin"></i>';
    uploadButton.style.pointerEvents = 'none';
    
    try {
        const response = await fetch('/api/upload-avatar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Cập nhật avatar thành công!');
            // Update the image source immediately
            const img = document.querySelector('.h-32.w-32 img');
            if (img) {
                img.src = result.data.avatar_url;
            }
            // Reload page to update session
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error(result.message || 'Upload failed');
        }
    } catch (error) {
        alert('Lỗi: ' + error.message);
    } finally {
        // Reset button state
        uploadButton.innerHTML = originalContent;
        uploadButton.style.pointerEvents = 'auto';
    }
});

// Close modal when clicking outside
document.getElementById('change-password-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeChangePasswordModal();
    }
});
</script>
@endpush
