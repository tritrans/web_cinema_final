@extends('layouts.admin')

@section('title', 'Tạo suất chiếu mới')

@php
    $breadcrumb = [
        ['title' => 'Quản lý rạp chiếu', 'url' => route('admin.theaters')],
        ['title' => 'Chi tiết rạp chiếu', 'url' => route('admin.theaters.show', $theater['id'])],
        ['title' => 'Tạo suất chiếu mới']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.theaters.show', $theater['id']) }}" 
           class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Quay lại
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tạo suất chiếu mới</h1>
            <p class="text-gray-600 mt-1">Tạo suất chiếu mới cho rạp "{{ $theater['name'] ?? 'N/A' }}"</p>
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
            <div class="flex items-start">
                <i data-lucide="alert-circle" class="h-5 w-5 mr-2 mt-0.5"></i>
                <div>
                    <h4 class="font-medium">Có lỗi xảy ra:</h4>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="calendar-plus" class="h-5 w-5"></i>
                    Thông tin suất chiếu
                </h3>
                
                <form method="POST" action="{{ route('admin.schedules.store', $theater['id']) }}" class="space-y-6">
                    @csrf
                    
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
                                        <option value="{{ $movie['id'] }}" {{ old('movie_id') == $movie['id'] ? 'selected' : '' }}>
                                            {{ $movie['title'] }} ({{ $movie['title_vi'] ?? '' }})
                                        </option>
                                    @endif
                                @endforeach
                            @else
                                <option value="" disabled>Không có phim nào khả dụng</option>
                            @endif
                        </select>
                    </div>

                    <!-- Room Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tên phòng chiếu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="room_name" value="{{ old('room_name') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               placeholder="Ví dụ: Phòng 1, Phòng VIP, Phòng IMAX" required>
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Thời gian bắt đầu <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               min="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Giá vé (VNĐ) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" value="{{ old('price') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               placeholder="Nhập giá vé" min="0" step="1000" required>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">
                            Trạng thái suất chiếu
                        </label>
                        <div class="toggle-switch">
                            <input type="checkbox" name="status" value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }} 
                                   id="status-toggle">
                            <span class="toggle-slider"></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.theaters.show', $theater['id']) }}" 
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i data-lucide="x" class="h-4 w-4"></i>
                            Hủy
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i data-lucide="calendar-plus" class="h-4 w-4"></i>
                            Tạo suất chiếu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Theater Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="building-2" class="h-5 w-5"></i>
                    Thông tin rạp chiếu
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tên rạp</label>
                        <p class="text-gray-900 font-medium">{{ $theater['name'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Địa chỉ</label>
                        <p class="text-gray-900">{{ $theater['address'] ?? 'N/A' }}</p>
                    </div>
                    @if(isset($theater['phone']) && $theater['phone'])
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                        <p class="text-gray-900">{{ $theater['phone'] }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                        <div class="mt-1">
                            @if($theater['is_active'] ?? false)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                    Đang hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i>
                                    Tạm dừng
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Text -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i data-lucide="info" class="h-5 w-5 text-blue-600 mr-2 mt-0.5"></i>
                    <div>
                        <h4 class="font-medium text-blue-900 mb-1">Lưu ý</h4>
                        <ul class="text-blue-800 text-sm space-y-1">
                            <li>• Thời gian bắt đầu phải sau thời điểm hiện tại</li>
                            <li>• Hệ thống sẽ tự động tính thời gian kết thúc dựa trên độ dài phim</li>
                            <li>• Kiểm tra xung đột với các suất chiếu khác</li>
                            <li>• Giá vé được tính bằng VNĐ</li>
                        </ul>
                    </div>
                </div>
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

    // Auto-calculate end time when movie or start time changes
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const movieSelect = document.querySelector('select[name="movie_id"]');
        const startTimeInput = document.querySelector('input[name="start_time"]');
        
        function updateEndTime() {
            const selectedMovieId = movieSelect.value;
            const startTime = startTimeInput.value;
            
            if (selectedMovieId && startTime) {
                // This would need to be implemented with AJAX to get movie duration
                // For now, we'll just show a placeholder
                console.log('Movie ID:', selectedMovieId, 'Start time:', startTime);
            }
        }
        
        movieSelect.addEventListener('change', updateEndTime);
        startTimeInput.addEventListener('change', updateEndTime);
    });
</script>
@endpush
