@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@php
    $breadcrumb = [
        ['title' => 'Quản lý người dùng']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Quản lý người dùng</h1>
            <p class="text-muted-foreground">Quản lý tài khoản người dùng trong hệ thống</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                Làm mới
            </button>
            <button onclick="exportUsers()" 
                    class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                <i data-lucide="download" class="h-4 w-4"></i>
                Xuất Excel
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $totalUsers = count($users);
            $adminUsers = count(array_filter($users, fn($u) => in_array($u['role'] ?? '', ['admin', 'review_manager', 'movie_manager', 'violation_manager'])));
            $activeUsers = count(array_filter($users, fn($u) => ($u['is_active'] ?? true)));
        @endphp

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Tổng người dùng</h3>
                <i data-lucide="users" class="h-4 w-4 text-blue-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($totalUsers) }}</div>
                <p class="text-xs text-muted-foreground">Tất cả tài khoản</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Quản trị viên</h3>
                <i data-lucide="shield" class="h-4 w-4 text-purple-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($adminUsers) }}</div>
                <p class="text-xs text-muted-foreground">Có quyền quản trị</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Quản lý chuyên môn</h3>
                <i data-lucide="user-check" class="h-4 w-4 text-green-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($adminUsers) }}</div>
                <p class="text-xs text-muted-foreground">Quản lý chuyên môn</p>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-card border border-border rounded-lg shadow-sm p-6">
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
            <input type="text" 
                   id="searchInput"
                   placeholder="Tìm kiếm người dùng theo tên hoặc email..."
                   class="w-full pl-10 pr-4 py-3 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent text-sm">
        </div>
        
        
    </div>

    <!-- Filters -->
    <div class="bg-card border border-border rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Vai trò</label>
                <select id="roleFilter" class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                    <option value="">Tất cả vai trò</option>
                    <option value="admin">Admin</option>
                    <option value="movie_manager">Movie Manager</option>
                    <option value="review_manager">Review Manager</option>
                    <option value="violation_manager">Violation Manager</option>
                    <option value="user">Người dùng thường</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Sắp xếp</label>
                <select id="sortBy" class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="name">Tên A-Z</option>
                    <option value="email">Email A-Z</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-card border border-border rounded-lg shadow-sm">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-3 px-4 font-medium text-sm w-1/3">Người dùng</th>
                            <th class="text-left py-3 px-4 font-medium text-sm w-1/4">Email</th>
                            <th class="text-left py-3 px-4 font-medium text-sm w-1/6">Vai trò</th>
                            <th class="text-left py-3 px-4 font-medium text-sm w-1/6">Ngày tham gia</th>
                            <th class="text-left py-3 px-4 font-medium text-sm w-1/12">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @if(count($users) > 0)
                            @foreach($users as $userItem)
                                <tr class="user-row border-b border-border hover:bg-muted/50"
                                    data-name="{{ strtolower($userItem['name'] ?? '') }}"
                                    data-email="{{ strtolower($userItem['email'] ?? '') }}"
                                    data-role="{{ $userItem['role'] ?? 'user' }}"
                                    data-created="{{ $userItem['created_at'] ?? '' }}">
                                    
                                    <td class="py-3 px-4 align-middle">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if(isset($userItem['avatar']) && $userItem['avatar'])
                                                    @php
                                                        $avatarUrl = $userItem['avatar'];
                                                        // If it's a relative path, prepend the web app URL
                                                        if (strpos($avatarUrl, 'http') !== 0) {
                                                            $avatarUrl = url('storage/' . $avatarUrl);
                                                        }
                                                    @endphp
                                                    <img src="{{ $avatarUrl }}" 
                                                         alt="Avatar" 
                                                         class="h-8 w-8 rounded-full object-cover"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center" style="display: none;">
                                                        <span class="text-primary-foreground text-sm font-medium">
                                                            {{ strtoupper(substr($userItem['name'] ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center">
                                                        <span class="text-primary-foreground text-sm font-medium">
                                                            {{ strtoupper(substr($userItem['name'] ?? 'U', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-sm truncate">{{ $userItem['name'] ?? 'N/A' }}</div>
                                                @if(isset($userItem['phone']))
                                                    <div class="text-xs text-muted-foreground truncate">{{ $userItem['phone'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        <div class="text-sm">{{ $userItem['email'] ?? 'N/A' }}</div>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col space-y-1">
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
                                                $role = $userItem['role'] ?? 'user';
                                                $colorClass = $roleColors[$role] ?? $roleColors['user'];
                                                $roleLabel = $roleLabels[$role] ?? 'Người dùng';
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                                {{ $roleLabel }}
                                            </span>
                                            @if(isset($userItem['is_active']) && !$userItem['is_active'])
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i data-lucide="lock" class="h-3 w-3 mr-1"></i>
                                                    Đã khóa
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                                    Hoạt động
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        <div class="text-sm text-muted-foreground">
                                            {{ isset($userItem['created_at']) ? \Carbon\Carbon::parse($userItem['created_at'])->format('d/m/Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        <div class="relative">
                                            <button onclick="toggleUserActions({{ $userItem['id'] ?? 0 }})" 
                                                    class="p-1 text-muted-foreground hover:text-foreground transition-colors">
                                                <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                                            </button>
                                            
                                            <!-- Dropdown Menu -->
                                            <div id="userActions{{ $userItem['id'] ?? 0 }}" 
                                                 class="hidden absolute right-0 mt-2 w-48 bg-white border border-border rounded-md shadow-lg z-10">
                                                <div class="py-1">
                                                    <button onclick="viewUser({{ $userItem['id'] ?? 0 }})" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                        <i data-lucide="eye" class="h-4 w-4 mr-2"></i>
                                                        Xem chi tiết
                                                    </button>
                                                    <button onclick="editUser({{ $userItem['id'] ?? 0 }})" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                        <i data-lucide="edit" class="h-4 w-4 mr-2"></i>
                                                        Chỉnh sửa
                                                    </button>
                                                    
                                                    <!-- Role Management -->
                                                    <div class="border-t border-border my-1"></div>
                                                    <button onclick="assignRole({{ $userItem['id'] ?? 0 }}, 'admin')" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                        <i data-lucide="shield" class="h-4 w-4 mr-2"></i>
                                                        Cấp quyền Admin
                                                    </button>
                                                    <button onclick="assignRole({{ $userItem['id'] ?? 0 }}, 'review_manager')" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                        <i data-lucide="star" class="h-4 w-4 mr-2"></i>
                                                        Cấp quyền Review Manager
                                                    </button>
                                                    <button onclick="assignRole({{ $userItem['id'] ?? 0 }}, 'movie_manager')" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                        <i data-lucide="film" class="h-4 w-4 mr-2"></i>
                                                        Cấp quyền Movie Manager
                                                    </button>
                                                    <button onclick="assignRole({{ $userItem['id'] ?? 0 }}, 'violation_manager')" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                        <i data-lucide="alert-triangle" class="h-4 w-4 mr-2"></i>
                                                        Cấp quyền Violation Manager
                                                    </button>
                                                    
                                                    <!-- Revoke Admin -->
                                                    @if(($userItem['role'] ?? '') === 'admin')
                                                        <button onclick="revokeAdminRole({{ $userItem['id'] ?? 0 }})" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                            <i data-lucide="x" class="h-4 w-4 mr-2"></i>
                                                            Hủy quyền admin
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Revoke Manager Roles -->
                                                    @if(in_array($userItem['role'] ?? '', ['review_manager', 'movie_manager', 'violation_manager']))
                                                        <button onclick="revokeManagerRole({{ $userItem['id'] ?? 0 }})" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                            <i data-lucide="user-minus" class="h-4 w-4 mr-2"></i>
                                                            Hủy quyền {{ ucfirst(str_replace('_', ' ', $userItem['role'] ?? '')) }}
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Status Management -->
                                                    <div class="border-t border-border my-1"></div>
                                                    @if(($userItem['is_active'] ?? true))
                                                        <button onclick="toggleUserStatus({{ $userItem['id'] ?? 0 }}, false)" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-orange-600 hover:bg-orange-50">
                                                            <i data-lucide="user-x" class="h-4 w-4 mr-2"></i>
                                                            Tạm khóa tài khoản
                                                        </button>
                                                    @else
                                                        <button onclick="toggleUserStatus({{ $userItem['id'] ?? 0 }}, true)" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                            <i data-lucide="user-check" class="h-4 w-4 mr-2"></i>
                                                            Kích hoạt tài khoản
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Delete User -->
                                                    <div class="border-t border-border my-1"></div>
                                                    <button onclick="deleteUser({{ $userItem['id'] ?? 0 }}, '{{ $userItem['name'] ?? 'N/A' }}')" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                                                        Xóa tài khoản
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <i data-lucide="users" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                                    <h3 class="text-lg font-medium text-foreground mb-2">Chưa có người dùng nào</h3>
                                    <p class="text-muted-foreground">Hệ thống chưa có người dùng nào được đăng ký.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Simple sort function that works immediately
function sortUsers(sortOption) {
    console.log('Sorting users with option:', sortOption);
    
    const userRows = Array.from(document.querySelectorAll('.user-row'));
    console.log('Found user rows:', userRows.length);
    
    if (userRows.length === 0) {
        console.log('No user rows found');
        return;
    }
    
    // Sort users
    userRows.sort((a, b) => {
        switch (sortOption) {
            case 'newest':
                return new Date(b.dataset.created) - new Date(a.dataset.created);
            case 'oldest':
                return new Date(a.dataset.created) - new Date(b.dataset.created);
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            case 'email':
                return a.dataset.email.localeCompare(b.dataset.email);
            default:
                return 0;
        }
    });
    
    // Re-append sorted rows to maintain order
    const tbody = document.getElementById('usersTableBody');
    userRows.forEach(row => {
        tbody.appendChild(row);
    });
    
    console.log('Sorting completed');
}

// Filter and sort function
function filterAndSortUsers() {
    console.log('Filtering and sorting users...');
    
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const sortBy = document.getElementById('sortBy');
    
    if (!searchInput || !roleFilter || !sortBy) {
        console.error('Filter elements not found');
        return;
    }
    
    const searchTerm = searchInput.value.toLowerCase();
    const selectedRole = roleFilter.value;
    const sortOption = sortBy.value;
    
    console.log('Filter criteria:', {
        searchTerm,
        selectedRole,
        sortOption
    });
    
    const userRows = Array.from(document.querySelectorAll('.user-row'));
    console.log('Total user rows found:', userRows.length);
    
    // Filter users
    const filteredRows = userRows.filter(row => {
        const name = row.dataset.name || '';
        const email = row.dataset.email || '';
        const role = row.dataset.role || '';
        
        // Search filter
        if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
            return false;
        }
        
        // Role filter
        if (selectedRole && role !== selectedRole) {
            return false;
        }
        
        return true;
    });
    
    console.log('Filtered rows count:', filteredRows.length);
    
    // Sort users
    filteredRows.sort((a, b) => {
        switch (sortOption) {
            case 'newest':
                return new Date(b.dataset.created) - new Date(a.dataset.created);
            case 'oldest':
                return new Date(a.dataset.created) - new Date(b.dataset.created);
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            case 'email':
                return a.dataset.email.localeCompare(b.dataset.email);
            default:
                return 0;
        }
    });
    
    // Hide all rows first
    userRows.forEach(row => {
        row.style.display = 'none';
    });
    
    // Show filtered and sorted rows
    filteredRows.forEach(row => {
        row.style.display = 'table-row';
    });
    
    // Show no results message if needed
    const tbody = document.getElementById('usersTableBody');
    let noResults = document.getElementById('noResults');
    
    if (filteredRows.length === 0) {
        if (!noResults) {
            const noResultsRow = document.createElement('tr');
            noResultsRow.id = 'noResults';
            noResultsRow.innerHTML = `
                <td colspan="5" class="text-center py-12">
                    <i data-lucide="search" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-foreground mb-2">Không tìm thấy người dùng nào</h3>
                    <p class="text-muted-foreground">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</p>
                </td>
            `;
            tbody.appendChild(noResultsRow);
            lucide.createIcons();
        }
    } else if (noResults) {
        noResults.remove();
    }
    
    console.log('Filtering completed');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Users page JavaScript loaded');
    
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const sortBy = document.getElementById('sortBy');
    
    // Check if elements exist
    if (!searchInput || !roleFilter || !sortBy) {
        console.error('Filter elements not found:', {
            searchInput: !!searchInput,
            roleFilter: !!roleFilter,
            sortBy: !!sortBy
        });
        return;
    }
    
    console.log('All filter elements found');
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative')) {
            document.querySelectorAll('[id^="userActions"]').forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
        }
    });
    
    // Add event listeners
    searchInput.addEventListener('input', function() {
        console.log('Search input changed:', searchInput.value);
        filterAndSortUsers();
    });
    
    roleFilter.addEventListener('change', function() {
        console.log('Role filter changed:', roleFilter.value);
        filterAndSortUsers();
    });
    
    sortBy.addEventListener('change', function() {
        console.log('Sort filter changed:', sortBy.value);
        // For sort, we can use the simple sort function
        sortUsers(sortBy.value);
    });
    
    // Initial filter run
    console.log('Running initial filter...');
    filterAndSortUsers();
});

// Toggle dropdown menu
function toggleUserActions(userId) {
    const dropdown = document.getElementById('userActions' + userId);
    const isHidden = dropdown.classList.contains('hidden');
    
    // Close all other dropdowns
    document.querySelectorAll('[id^="userActions"]').forEach(d => {
        d.classList.add('hidden');
    });
    
    // Toggle current dropdown
    if (isHidden) {
        dropdown.classList.remove('hidden');
    }
}

// User management functions
function viewUser(userId) {
    // Close dropdown
    document.getElementById('userActions' + userId).classList.add('hidden');
    
    // Redirect to view user page
    window.location.href = `/admin/users/${userId}`;
}

function editUser(userId) {
    // Close dropdown
    document.getElementById('userActions' + userId).classList.add('hidden');
    
    // Redirect to edit user page
    window.location.href = `/admin/users/${userId}/edit`;
}

function assignRole(userId, role) {
    // Close dropdown
    document.getElementById('userActions' + userId).classList.add('hidden');
    
    const roleNames = {
        'admin': 'Admin',
        'review_manager': 'Review Manager',
        'movie_manager': 'Movie Manager',
        'violation_manager': 'Violation Manager'
    };
    
    if (confirm(`Bạn có chắc chắn muốn cấp quyền ${roleNames[role]} cho người dùng này?`)) {
        // Call API to assign role
        fetch(`/admin/users/${userId}/assign-role`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ role: role })
        })
        .then(response => {
            console.log('Assign role response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Assign role data:', data);
            if (data.success) {
                showNotification('Cấp quyền thành công!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
                console.error('Assign role error:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi cấp quyền: ' + error.message, 'error');
        });
    }
}

function revokeAdminRole(userId) {
    // Close dropdown
    document.getElementById('userActions' + userId).classList.add('hidden');
    
    if (confirm('Bạn có chắc chắn muốn hủy quyền admin của người dùng này?')) {
        // Call API to revoke admin role
        fetch(`/admin/users/${userId}/revoke-admin`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Revoke admin response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Revoke admin data:', data);
            if (data.success) {
                showNotification('Hủy quyền admin thành công!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification(data.message || 'Có lỗi xảy ra', 'error');
                console.error('Revoke admin error:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Có lỗi xảy ra khi hủy quyền: ' + error.message, 'error');
        });
    }
}

function revokeManagerRole(userId) {
    // Close dropdown
    document.getElementById('userActions' + userId).classList.add('hidden');
    
    if (confirm('Bạn có chắc chắn muốn hủy quyền manager của người dùng này? Người dùng sẽ trở về vai trò "Người dùng" thông thường.')) {
        // Call API to revoke manager role (assign user role)
        fetch(`/admin/users/${userId}/assign-role`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ role: 'user' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Hủy quyền manager thành công! Người dùng đã trở về vai trò "Người dùng".', 'success');
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
    // Close dropdown
    document.getElementById('userActions' + userId).classList.add('hidden');
    
    const action = activate ? 'kích hoạt' : 'tạm khóa';
    if (confirm(`Bạn có chắc chắn muốn ${action} người dùng này?`)) {
        // Call API to toggle user status
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
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
    // Close dropdown
    document.getElementById('userActions' + userId).classList.add('hidden');
    
    if (confirm(`Bạn có chắc chắn muốn xóa tài khoản "${userName}"? Hành động này không thể hoàn tác!`)) {
        // Call API to delete user
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
                setTimeout(() => window.location.reload(), 1000);
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

function exportUsers() {
    // Call API to export users
    fetch('/admin/users/export', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Export failed');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'users_export_' + new Date().toISOString().split('T')[0] + '.xlsx';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        showNotification('Xuất danh sách người dùng thành công!', 'success');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xuất danh sách', 'error');
    });
}


// Notification function
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
