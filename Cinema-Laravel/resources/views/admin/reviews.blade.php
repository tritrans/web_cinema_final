@extends('layouts.app')

@section('title', 'Quản lý đánh giá - Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Quản lý đánh giá</h1>
            <p class="text-gray-600">Danh sách tất cả đánh giá trong hệ thống</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">Tổng: {{ count($reviews) }} đánh giá</span>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}
        </div>
    @endif

    <!-- Reviews Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if(count($reviews) > 0)
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
                                Đánh giá
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bình luận
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
                        @foreach($reviews as $review)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $review['user']['avatar'] ?? 'https://via.placeholder.com/40' }}" 
                                             alt="{{ $review['user']['name'] }}" 
                                             class="h-10 w-10 rounded-full">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $review['user']['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $review['user']['email'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $review['movie']['title'] ?? 'N/A' }}</div>
                                    @if(isset($review['movie']['title_vi']))
                                        <div class="text-sm text-gray-500">{{ $review['movie']['title_vi'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-{{ $i <= $review['rating'] ? 'yellow-400' : 'gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-2 text-sm font-medium text-gray-900">{{ $review['rating'] }}/5</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ $review['comment'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($review['created_at'])->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-indigo-600 hover:text-indigo-900" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Xóa" onclick="confirmDelete({{ $review['id'] }})">
                                            <i class="fas fa-trash"></i>
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
                <i class="fas fa-star text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Chưa có đánh giá nào</h3>
                <p class="text-gray-600">Hệ thống chưa có đánh giá nào được tạo</p>
            </div>
        @endif
    </div>

    <!-- Statistics -->
    @if(count($reviews) > 0)
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tổng đánh giá</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($reviews) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đánh giá trung bình</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count($reviews) > 0 ? number_format(array_sum(array_column($reviews, 'rating')) / count($reviews), 1) : 0 }}/5
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-star text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Đánh giá 5 sao</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($reviews, fn($r) => $r['rating'] == 5)) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Hôm nay</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ count(array_filter($reviews, fn($r) => \Carbon\Carbon::parse($r['created_at'])->isToday())) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Phân bố đánh giá</h3>
            <div class="space-y-3">
                @for($rating = 5; $rating >= 1; $rating--)
                    @php
                        $count = count(array_filter($reviews, fn($r) => $r['rating'] == $rating));
                        $percentage = count($reviews) > 0 ? ($count / count($reviews)) * 100 : 0;
                    @endphp
                    <div class="flex items-center">
                        <div class="w-8 text-sm font-medium text-gray-700">{{ $rating }} sao</div>
                        <div class="flex-1 mx-4">
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="w-12 text-sm text-gray-600">{{ $count }}</div>
                        <div class="w-12 text-sm text-gray-500">{{ number_format($percentage, 1) }}%</div>
                    </div>
                @endfor
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(reviewId) {
    if (confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
        // Implement delete functionality
        console.log('Delete review:', reviewId);
    }
}
</script>
@endpush
