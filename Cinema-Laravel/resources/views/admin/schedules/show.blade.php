@extends('layouts.admin')

@section('title', 'Chi tiết suất chiếu')

@php
    $breadcrumb = [
        ['title' => 'Quản lý rạp chiếu', 'url' => route('admin.theaters')],
        ['title' => 'Chi tiết suất chiếu']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.theaters') }}" 
           class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Quay lại
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết suất chiếu</h1>
            <p class="text-gray-600 mt-1">Thông tin chi tiết về suất chiếu</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Schedule Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="calendar" class="h-5 w-5"></i>
                    Thông tin suất chiếu
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phim</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $schedule['movie']['title'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rạp chiếu</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $schedule['theater']['name'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phòng chiếu</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $schedule['room_name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Giá vé</label>
                            <p class="text-lg font-semibold text-primary">{{ number_format($schedule['price'] ?? 0) }}đ</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian bắt đầu</label>
                            <p class="flex items-center gap-2">
                                <i data-lucide="clock" class="h-4 w-4 text-gray-500"></i>
                                <span class="text-gray-900">
                                    @if(isset($schedule['start_time']))
                                        {{ \Carbon\Carbon::parse($schedule['start_time'])->locale('vi')->isoFormat('DD/MM/YYYY HH:mm') }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian kết thúc</label>
                            <p class="flex items-center gap-2">
                                <i data-lucide="clock" class="h-4 w-4 text-gray-500"></i>
                                <span class="text-gray-900">
                                    @if(isset($schedule['end_time']))
                                        {{ \Carbon\Carbon::parse($schedule['end_time'])->locale('vi')->isoFormat('DD/MM/YYYY HH:mm') }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                        <div class="mt-1">
                            @if(($schedule['status'] ?? 'active') === 'active')
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

            <!-- Movie Information -->
            @if(isset($schedule['movie']))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="film" class="h-5 w-5"></i>
                    Thông tin phim
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tên phim</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $schedule['movie']['title'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thời lượng</label>
                            <p class="text-gray-900">{{ $schedule['movie']['duration'] ?? 'N/A' }} phút</p>
                        </div>
                    </div>

                    @if(isset($schedule['movie']['genres']) && count($schedule['movie']['genres']) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thể loại</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($schedule['movie']['genres'] as $genre)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded font-medium">
                                    {{ $genre['name'] ?? 'N/A' }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($schedule['movie']['rating']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá</label>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i data-lucide="star" class="h-4 w-4 {{ $i <= $schedule['movie']['rating'] ? 'text-yellow-500 fill-current' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <span class="text-gray-700 font-medium">{{ number_format($schedule['movie']['rating'], 1) }}/5</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.schedules.edit', $schedule['id']) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i data-lucide="edit" class="h-4 w-4"></i>
                        Chỉnh sửa suất chiếu
                    </a>
                    <button onclick="deleteSchedule({{ $schedule['id'] }})" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        Xóa suất chiếu
                    </button>
                </div>
            </div>

            <!-- Schedule Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin hệ thống</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID:</span>
                        <span class="font-medium">{{ $schedule['id'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ngày tạo:</span>
                        <span class="font-medium">
                            @if(isset($schedule['created_at']))
                                {{ \Carbon\Carbon::parse($schedule['created_at'])->locale('vi')->isoFormat('DD/MM/YYYY HH:mm') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cập nhật lần cuối:</span>
                        <span class="font-medium">
                            @if(isset($schedule['updated_at']))
                                {{ \Carbon\Carbon::parse($schedule['updated_at'])->locale('vi')->isoFormat('DD/MM/YYYY HH:mm') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Xác nhận xóa suất chiếu</h3>
                <p class="text-sm text-gray-600">Hành động này không thể hoàn tác</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">
            Bạn có chắc chắn muốn xóa suất chiếu này? 
            Tất cả thông tin liên quan sẽ bị xóa vĩnh viễn.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Hủy
            </button>
            <button onclick="confirmDeleteSchedule()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Xóa suất chiếu
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let scheduleToDelete = null;

    function deleteSchedule(scheduleId) {
        scheduleToDelete = scheduleId;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
        scheduleToDelete = null;
    }

    function confirmDeleteSchedule() {
        if (scheduleToDelete) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/schedules/${scheduleToDelete}`;
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            const tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = '{{ csrf_token() }}';
            
            form.appendChild(methodField);
            form.appendChild(tokenField);
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
