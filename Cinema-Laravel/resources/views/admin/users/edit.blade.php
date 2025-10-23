@extends('layouts.admin')

@section('title', 'Chỉnh sửa người dùng')

@php
    $breadcrumb = [
        ['title' => 'Quản lý người dùng', 'url' => route('admin.users')],
        ['title' => 'Chi tiết người dùng', 'url' => route('admin.users.view', $userData['id'])],
        ['title' => 'Chỉnh sửa']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Chỉnh sửa người dùng</h1>
            <p class="text-muted-foreground">Cập nhật thông tin người dùng trong hệ thống</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.view', $userData['id']) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="max-w-2xl">
        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <form id="editUserForm" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2">Họ và tên *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ $userData['name'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent"
                                   required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">Email *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ $userData['email'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent"
                                   required>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2">Số điện thoại</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ $userData['phone'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Role and Status -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Vai trò và trạng thái</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Vai trò hiện tại</label>
                            @php
                                $roleColors = [
                                    'admin' => 'bg-purple-100 text-purple-800',
                                    'movie_manager' => 'bg-blue-100 text-blue-800',
                                    'review_manager' => 'bg-green-100 text-green-800',
                                    'violation_manager' => 'bg-red-100 text-red-800',
                                    'user' => 'bg-gray-100 text-gray-800'
                                ];
                                $roleLabels = [
                                    'admin' => 'Admin',
                                    'movie_manager' => 'Movie Manager',
                                    'review_manager' => 'Review Manager',
                                    'violation_manager' => 'Violation Manager',
                                    'user' => 'Người dùng'
                                ];
                                $role = $userData['role'] ?? 'user';
                                $colorClass = $roleColors[$role] ?? $roleColors['user'];
                                $roleLabel = $roleLabels[$role] ?? 'Người dùng';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $colorClass }}">
                                {{ $roleLabel }}
                            </span>
                            <p class="text-xs text-muted-foreground mt-1">
                                Để thay đổi vai trò, sử dụng các nút cấp quyền trong trang chi tiết
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-2">Trạng thái hiện tại</label>
                            @if(($userData['is_active'] ?? true))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="h-4 w-4 mr-1"></i>
                                    Đang hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i data-lucide="x-circle" class="h-4 w-4 mr-1"></i>
                                    Tạm khóa
                                </span>
                            @endif
                            <p class="text-xs text-muted-foreground mt-1">
                                Để thay đổi trạng thái, sử dụng các nút trong trang chi tiết
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Thông tin tài khoản</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground">Ngày tham gia</label>
                            <p class="text-sm">
                                {{ isset($userData['created_at']) ? \Carbon\Carbon::parse($userData['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground">Cập nhật lần cuối</label>
                            <p class="text-sm">
                                {{ isset($userData['updated_at']) ? \Carbon\Carbon::parse($userData['updated_at'])->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                        @if(isset($userData['last_login_at']))
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground">Lần đăng nhập cuối</label>
                            <p class="text-sm">
                                {{ \Carbon\Carbon::parse($userData['last_login_at'])->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-border">
                    <a href="{{ route('admin.users.view', $userData['id']) }}" 
                       class="px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                        Hủy
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 rounded-md text-sm font-medium">
                        <i data-lucide="save" class="h-4 w-4 mr-2"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editUserForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 mr-2 animate-spin"></i>Đang lưu...';
        submitBtn.disabled = true;
        
        fetch(`/admin/users/{{ $userData['id'] }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cập nhật thông tin người dùng thành công!', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/users/{{ $userData['id'] }}';
                }, 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi cập nhật thông tin', 'error');
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-md shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endsection
