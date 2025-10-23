@extends('layouts.admin')

@section('title', 'Chỉnh sửa suất chiếu')

@php
    $breadcrumb = [
        ['title' => 'Quản lý rạp chiếu', 'url' => route('admin.theaters')],
        ['title' => 'Chi tiết suất chiếu', 'url' => route('admin.schedules.show', $schedule['id'])],
        ['title' => 'Chỉnh sửa suất chiếu']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.schedules.show', $schedule['id']) }}" 
           class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Quay lại
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa suất chiếu</h1>
            <p class="text-gray-600 mt-1">Cập nhật thông tin suất chiếu</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="h-5 w-5 mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-center mb-2">
                <i data-lucide="alert-circle" class="h-5 w-5 mr-2"></i>
                <span class="font-medium">Có lỗi xảy ra:</span>
            </div>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Thông tin suất chiếu</h3>
                
                <form method="POST" action="{{ route('admin.schedules.update', $schedule['id']) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Movie Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Chọn phim <span class="text-red-500">*</span>
                        </label>
                        <select name="movie_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            <option value="">-- Chọn phim --</option>
                            @if(isset($movies) && is_array($movies))
                                @foreach($movies as $movie)
                                    @if(isset($movie['id']) && isset($movie['title']))
                                        <option value="{{ $movie['id'] }}" {{ old('movie_id', $schedule['movie_id'] ?? '') == $movie['id'] ? 'selected' : '' }}>
                                            {{ $movie['title'] }} ({{ $movie['title_vi'] ?? '' }})
                                        </option>
                                    @endif
                                @endforeach
                            @else
                                <option value="" disabled>Không có phim nào khả dụng</option>
                            @endif
                        </select>
                    </div>

                    <!-- Theater Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Rạp chiếu <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="theater_id" value="{{ $schedule['theater_id'] ?? '' }}">
                        <input type="text" value="{{ $theater['name'] ?? 'N/A' }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                               readonly>
                    </div>

                    <!-- Room Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tên phòng chiếu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="room_name" value="{{ old('room_name', $schedule['room_name'] ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               placeholder="Ví dụ: Phòng 1, Phòng VIP, Phòng IMAX" required>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Thời gian bắt đầu <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_time" 
                               value="{{ old('start_time', isset($schedule['start_time']) ? \Carbon\Carbon::parse($schedule['start_time'])->format('Y-m-d\TH:i') : '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               min="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Giá vé (VNĐ) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" value="{{ old('price', $schedule['price'] ?? '') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               placeholder="Nhập giá vé" min="0" step="1000" required>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                            <select name="status" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                                <option value="active" {{ old('status', $schedule['status'] ?? 'active') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="inactive" {{ old('status', $schedule['status'] ?? 'active') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.schedules.show', $schedule['id']) }}" 
                           class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Hủy
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-medium">
                            Cập nhật suất chiếu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Current Schedule Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin hiện tại</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phim:</span>
                        <span class="font-medium">{{ $schedule['movie']['title'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rạp:</span>
                        <span class="font-medium">{{ $theater['name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phòng:</span>
                        <span class="font-medium">{{ $schedule['room_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Giá vé:</span>
                        <span class="font-medium">{{ number_format($schedule['price'] ?? 0) }}đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Trạng thái:</span>
                        <span class="font-medium">
                            @if(($schedule['status'] ?? 'active') === 'active')
                                <span class="text-green-600">Hoạt động</span>
                            @else
                                <span class="text-red-600">Tạm dừng</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">Lưu ý</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Thời gian bắt đầu phải sau thời điểm hiện tại</li>
                    <li>• Thời gian kết thúc sẽ tự động tính dựa trên thời lượng phim</li>
                    <li>• Giá vé phải lớn hơn hoặc bằng 0</li>
                    <li>• Không thể chỉnh sửa suất chiếu đã có vé đặt</li>
                </ul>
            </div>

            <!-- Room Suggestions -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Gợi ý tên phòng</h4>
                <div class="space-y-1">
                    <button type="button" onclick="setRoomName('Phòng 1')" 
                            class="block w-full text-left px-2 py-1 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded">
                        Phòng 1
                    </button>
                    <button type="button" onclick="setRoomName('Phòng 2')" 
                            class="block w-full text-left px-2 py-1 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded">
                        Phòng 2
                    </button>
                    <button type="button" onclick="setRoomName('Phòng VIP')" 
                            class="block w-full text-left px-2 py-1 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded">
                        Phòng VIP
                    </button>
                    <button type="button" onclick="setRoomName('Phòng IMAX')" 
                            class="block w-full text-left px-2 py-1 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded">
                        Phòng IMAX
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setRoomName(roomName) {
    document.querySelector('input[name="room_name"]').value = roomName;
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endpush
