@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@php
    $breadcrumb = [
        ['title' => 'Quản lý người dùng', 'url' => route('admin.users')],
        ['title' => 'Chi tiết người dùng']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Chi tiết người dùng</h1>
            <p class="text-muted-foreground">Thông tin chi tiết về người dùng trong hệ thống</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Quay lại
            </a>
            <a href="{{ route('admin.users.edit', $userData['id']) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 rounded-md text-sm font-medium">
                <i data-lucide="edit" class="h-4 w-4"></i>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <!-- User Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Info Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-card border border-border rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Thông tin cơ bản</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        @if(isset($userData['avatar']) && $userData['avatar'])
                            <img src="{{ $userData['avatar'] }}" alt="Avatar" class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-primary flex items-center justify-center">
                                <span class="text-primary-foreground text-xl font-bold">
                                    {{ strtoupper(substr($userData['name'] ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-xl font-semibold">{{ $userData['name'] ?? 'N/A' }}</h4>
                            <p class="text-muted-foreground">{{ $userData['email'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground">Email</label>
                            <p class="text-sm">{{ $userData['email'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-muted-foreground">Số điện thoại</label>
                            <p class="text-sm">{{ $userData['phone'] ?? 'Chưa cập nhật' }}</p>
                        </div>
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
                    </div>
                </div>
            </div>

            <!-- Role and Status -->
            <div class="bg-card border border-border rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Vai trò và trạng thái</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground">Vai trò</label>
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
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-muted-foreground">Trạng thái</label>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-card border border-border rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Thao tác nhanh</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.users.edit', $userData['id']) }}" 
                       class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted rounded-md">
                        <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                        Chỉnh sửa thông tin
                    </a>
                    
                    @if(($userData['role'] ?? '') !== 'admin')
                        <button onclick="assignRole({{ $userData['id'] }}, 'admin')" 
                                class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted rounded-md">
                            <i data-lucide="shield" class="h-4 w-4 mr-2"></i>
                            Cấp quyền Admin
                        </button>
                    @endif
                    
                    @if(($userData['role'] ?? '') === 'admin')
                        <button onclick="revokeAdminRole({{ $userData['id'] }})" 
                                class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted rounded-md">
                            <i data-lucide="x" class="h-4 w-4 mr-2"></i>
                            Hủy quyền admin
                        </button>
                    @endif
                    
                    @if(($userData['is_active'] ?? true))
                        <button onclick="toggleUserStatus({{ $userData['id'] }}, false)" 
                                class="flex items-center w-full px-4 py-2 text-sm text-orange-600 hover:bg-orange-50 rounded-md">
                            <i data-lucide="user-x" class="h-4 w-4 mr-2"></i>
                            Tạm khóa tài khoản
                        </button>
                    @else
                        <button onclick="toggleUserStatus({{ $userData['id'] }}, true)" 
                                class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-green-50 rounded-md">
                            <i data-lucide="user-check" class="h-4 w-4 mr-2"></i>
                            Kích hoạt tài khoản
                        </button>
                    @endif
                    
                    @if($user['role'] === 'admin')
                        <button onclick="deleteUser({{ $userData['id'] }}, '{{ $userData['name'] ?? 'N/A' }}')" 
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                            <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                            Xóa tài khoản
                        </button>
                    @endif
                </div>
            </div>

            <!-- User Stats -->
            <div class="bg-card border border-border rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Thống kê</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-muted-foreground">Số lần đăng nhập</span>
                        <span class="text-sm font-medium">{{ $userData['login_count'] ?? '0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-muted-foreground">Lần đăng nhập cuối</span>
                        <span class="text-sm font-medium">
                            {{ isset($userData['last_login_at']) ? \Carbon\Carbon::parse($userData['last_login_at'])->format('d/m/Y H:i') : 'Chưa có' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Include the same functions from index.blade.php
function assignRole(userId, role) {
    const roleNames = {
        'admin': 'Admin',
        'review_manager': 'Review Manager',
        'movie_manager': 'Movie Manager',
        'violation_manager': 'Violation Manager'
    };
    
    if (confirm(`Bạn có chắc chắn muốn cấp quyền ${roleNames[role]} cho người dùng này?`)) {
        fetch(`/admin/users/${userId}/assign-role`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ role: role })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Cấp quyền thành công!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi cấp quyền', 'error');
        });
    }
}

function revokeAdminRole(userId) {
    if (confirm('Bạn có chắc chắn muốn hủy quyền admin của người dùng này?')) {
        fetch(`/admin/users/${userId}/revoke-admin`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Hủy quyền admin thành công!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi hủy quyền', 'error');
        });
    }
}

function toggleUserStatus(userId, activate) {
    const action = activate ? 'kích hoạt' : 'tạm khóa';
    if (confirm(`Bạn có chắc chắn muốn ${action} người dùng này?`)) {
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ activate: activate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`${action.charAt(0).toUpperCase() + action.slice(1)} người dùng thành công!`, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(`Có lỗi xảy ra khi ${action} người dùng`, 'error');
        });
    }
}

function deleteUser(userId, userName) {
    if (confirm(`Bạn có chắc chắn muốn xóa tài khoản "${userName}"? Hành động này không thể hoàn tác!`)) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Xóa người dùng thành công!', 'success');
                setTimeout(() => window.location.href = '/admin/users', 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi xóa người dùng', 'error');
        });
    }
}

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
