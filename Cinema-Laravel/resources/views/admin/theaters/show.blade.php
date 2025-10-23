@extends('layouts.admin')

@section('title', 'Chi tiết rạp chiếu')

@php
    $breadcrumb = [
        ['title' => 'Quản lý rạp chiếu', 'url' => route('admin.theaters')],
        ['title' => 'Chi tiết rạp chiếu']
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
            <h1 class="text-3xl font-bold text-gray-900">Chi tiết rạp chiếu</h1>
            <p class="text-gray-600 mt-1">Thông tin chi tiết về rạp chiếu "{{ $theater['name'] ?? 'N/A' }}"</p>
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
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="building-2" class="h-5 w-5"></i>
                    Thông tin cơ bản
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tên rạp chiếu</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $theater['name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                        <p class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="h-4 w-4 mt-0.5 text-gray-500"></i>
                            <span class="text-gray-900">{{ $theater['address'] ?? 'N/A' }}</span>
                        </p>
                    </div>

                    @if(isset($theater['phone']) && $theater['phone'])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                        <p class="flex items-center gap-2">
                            <i data-lucide="phone" class="h-4 w-4 text-gray-500"></i>
                            <span class="text-gray-900">{{ $theater['phone'] }}</span>
                        </p>
                    </div>
                    @endif

                    @if(isset($theater['email']) && $theater['email'])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <p class="flex items-center gap-2">
                            <i data-lucide="mail" class="h-4 w-4 text-gray-500"></i>
                            <span class="text-gray-900">{{ $theater['email'] }}</span>
                        </p>
                    </div>
                    @endif

                    @if(isset($theater['description']) && $theater['description'])
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                        <p class="text-gray-900">{{ $theater['description'] }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Theater Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="h-5 w-5"></i>
                    Thống kê rạp chiếu
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ count($theater['rooms'] ?? []) }}</div>
                        <div class="text-sm text-blue-800">Phòng chiếu</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ array_sum(array_map(fn($r) => $r['seat_count'] ?? 0, $theater['rooms'] ?? [])) }}</div>
                        <div class="text-sm text-green-800">Tổng số ghế</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ count($theater['schedules'] ?? []) }}</div>
                        <div class="text-sm text-purple-800">Suất chiếu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.theaters.edit', $theater['id']) }}" 
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i data-lucide="edit" class="h-4 w-4"></i>
                        Chỉnh sửa rạp
                    </a>
                    <button onclick="deleteTheater({{ $theater['id'] }}, '{{ $theater['name'] }}')" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        Xóa rạp
                    </button>
                </div>
            </div>

            <!-- Theater Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin hệ thống</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID:</span>
                        <span class="font-medium">{{ $theater['id'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ngày tạo:</span>
                        <span class="font-medium">
                            @if(isset($theater['created_at']))
                                {{ \Carbon\Carbon::parse($theater['created_at'])->locale('vi')->isoFormat('DD/MM/YYYY HH:mm') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cập nhật lần cuối:</span>
                        <span class="font-medium">
                            @if(isset($theater['updated_at']))
                                {{ \Carbon\Carbon::parse($theater['updated_at'])->locale('vi')->isoFormat('DD/MM/YYYY HH:mm') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Rooms List -->
            @if(isset($theater['rooms']) && count($theater['rooms']) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="users" class="h-5 w-5"></i>
                    Danh sách phòng chiếu
                </h3>
                <div class="space-y-2">
                    @foreach($theater['rooms'] as $room)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium">{{ $room['name'] ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-600">{{ $room['seat_count'] ?? 0 }} ghế</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $room['type'] ?? 'Standard' }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
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
                <h3 class="text-lg font-semibold text-gray-900">Xác nhận xóa rạp chiếu</h3>
                <p class="text-sm text-gray-600">Hành động này không thể hoàn tác</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">
            Bạn có chắc chắn muốn xóa rạp chiếu "<span id="theaterName"></span>"? 
            Tất cả thông tin liên quan sẽ bị xóa vĩnh viễn.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                Hủy
            </button>
            <button onclick="confirmDeleteTheater()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Xóa rạp chiếu
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let theaterToDelete = null;

    function deleteTheater(theaterId, theaterName) {
        theaterToDelete = theaterId;
        document.getElementById('theaterName').textContent = theaterName;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
        theaterToDelete = null;
    }

    function confirmDeleteTheater() {
        if (theaterToDelete) {
            window.location.href = `/admin/theaters/${theaterToDelete}/delete`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
