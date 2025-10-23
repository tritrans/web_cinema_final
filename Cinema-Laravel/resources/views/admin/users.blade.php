@extends('layouts.app')

@section('title', 'Quản lý người dùng - Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quản lý người dùng</h1>
            <p class="text-gray-600">Danh sách tất cả người dùng trong hệ thống</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">Tổng: {{ count($users) }} người dùng</span>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if(count($users) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Người dùng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vai trò
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày tạo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Trạng thái
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $user['avatar'] ?? 'https://via.placeholder.com/40' }}" 
                                             alt="{{ $user['name'] }}" 
                                             class="h-10 w-10 rounded-full">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user['name'] }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $user['id'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user['email'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($user['role'] === 'super_admin') bg-red-100 text-red-800
                                        @elseif($user['role'] === 'admin') bg-purple-100 text-purple-800
                                        @elseif($user['role'] === 'review_manager') bg-yellow-100 text-yellow-800
                                        @elseif($user['role'] === 'movie_manager') bg-blue-100 text-blue-800
                                        @elseif($user['role'] === 'violation_manager') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $user['role'])) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($user['created_at'])->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Hoạt động
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-indigo-600 hover:text-indigo-900" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($user['role'] !== 'super_admin')
                                            <button class="text-red-600 hover:text-red-900" title="Xóa" onclick="confirmDelete({{ $user['id'] }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Chưa có người dùng nào</h3>
                <p class="text-gray-600">Hệ thống chưa có người dùng nào được đăng ký</p>
            </div>
        @endif
    </div>

    <!-- Statistics -->
    @if(count($users) > 0)
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng người dùng</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($users) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-crown text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Quản trị viên</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($users, fn($u) => in_array($u['role'], ['admin', 'super_admin']))) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-user text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Người dùng thường</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($users, fn($u) => $u['role'] === 'user')) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-user-tie text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Quản lý</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($users, fn($u) => in_array($u['role'], ['review_manager', 'movie_manager', 'violation_manager']))) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(userId) {
    if (confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
        // Implement delete functionality
        console.log('Delete user:', userId);
    }
}
</script>
@endpush
