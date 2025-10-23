@extends('layouts.app')

@section('title', 'Quản lý bình luận - Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quản lý bình luận</h1>
            <p class="text-gray-600">Danh sách tất cả bình luận trong hệ thống</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">Tổng: {{ count($comments) }} bình luận</span>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}
        </div>
    @endif

    <!-- Comments Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if(count($comments) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Người dùng
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Phim
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nội dung
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loại
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày tạo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($comments as $comment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $comment['user']['avatar'] ?? 'https://via.placeholder.com/40' }}" 
                                             alt="{{ $comment['user']['name'] }}" 
                                             class="h-10 w-10 rounded-full">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $comment['user']['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $comment['user']['email'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $comment['movie']['title'] ?? 'N/A' }}</div>
                                    @if(isset($comment['movie']['title_vi']))
                                        <div class="text-sm text-gray-500">{{ $comment['movie']['title_vi'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs">
                                        <div class="line-clamp-3">{{ $comment['content'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($comment['parent_id'])
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-reply mr-1"></i>Phản hồi
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-comment mr-1"></i>Bình luận
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($comment['created_at'])->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-indigo-600 hover:text-indigo-900" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Xóa" onclick="confirmDelete({{ $comment['id'] }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="text-orange-600 hover:text-orange-900" title="Ẩn bình luận">
                                            <i class="fas fa-eye-slash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Chưa có bình luận nào</h3>
                <p class="text-gray-600">Hệ thống chưa có bình luận nào được tạo</p>
            </div>
        @endif
    </div>

    <!-- Statistics -->
    @if(count($comments) > 0)
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-comments text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng bình luận</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($comments) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-comment text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Bình luận gốc</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($comments, fn($c) => !$c['parent_id'])) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-reply text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Phản hồi</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($comments, fn($c) => $c['parent_id'])) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-calendar text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Hôm nay</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($comments, fn($c) => \Carbon\Carbon::parse($c['created_at'])->isToday())) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Comments -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Bình luận gần đây</h3>
            <div class="space-y-4">
                @foreach(array_slice($comments, 0, 5) as $comment)
                    <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg">
                        <img src="{{ $comment['user']['avatar'] ?? 'https://via.placeholder.com/40' }}" 
                             alt="{{ $comment['user']['name'] }}" 
                             class="w-10 h-10 rounded-full">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-800">{{ $comment['user']['name'] }}</h4>
                                <span class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($comment['created_at'])->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <p class="text-gray-700 mb-2">{{ $comment['content'] }}</p>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>Phim: {{ $comment['movie']['title'] ?? 'N/A' }}</span>
                                @if($comment['parent_id'])
                                    <span class="text-blue-600">
                                        <i class="fas fa-reply mr-1"></i>Phản hồi
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(commentId) {
    if (confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
        // Implement delete functionality
        console.log('Delete comment:', commentId);
    }
}
</script>
@endpush
