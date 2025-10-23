@extends('layouts.admin')

@section('title', 'Quản lý rạp chiếu')

@php
    $breadcrumb = [
        ['title' => 'Quản lý rạp chiếu']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Quản lý rạp chiếu</h1>
            <p class="text-muted-foreground">Quản lý thông tin rạp chiếu và phòng chiếu</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                Làm mới
            </button>
            <a href="{{ route('admin.theaters.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 rounded-md text-sm font-medium">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Thêm rạp mới
            </a>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $totalTheaters = count($theaters);
            $activeTheaters = count(array_filter($theaters, fn($t) => $t['is_active'] ?? true));
            $totalRooms = array_sum(array_map(fn($t) => count($t['rooms'] ?? []), $theaters));
            $totalSeats = array_sum(array_map(fn($t) => array_sum(array_map(fn($r) => $r['seat_count'] ?? 0, $t['rooms'] ?? [])), $theaters));
        @endphp

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Tổng số rạp</h3>
                <i data-lucide="building-2" class="h-4 w-4 text-blue-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($totalTheaters) }}</div>
                <p class="text-xs text-muted-foreground">Rạp trong hệ thống</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Đang hoạt động</h3>
                <i data-lucide="activity" class="h-4 w-4 text-green-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($activeTheaters) }}</div>
                <p class="text-xs text-muted-foreground">Rạp đang mở cửa</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Phòng chiếu</h3>
                <i data-lucide="monitor" class="h-4 w-4 text-purple-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($totalRooms) }}</div>
                <p class="text-xs text-muted-foreground">Tổng phòng chiếu</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Ghế ngồi</h3>
                <i data-lucide="armchair" class="h-4 w-4 text-orange-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($totalSeats) }}</div>
                <p class="text-xs text-muted-foreground">Tổng ghế ngồi</p>
            </div>
        </div>
    </div>


    <!-- Theaters List -->
    <div class="bg-card border border-border rounded-lg shadow-sm">
        <div class="p-6">
            <div class="space-y-6">
                @if(isset($theaters) && is_array($theaters) && count($theaters) > 0)
                    @foreach($theaters as $theater)
                        <div class="border border-border rounded-lg p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold">{{ $theater['name'] ?? 'N/A' }}</h3>
                                        @if($theater['is_active'] ?? true)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                                Hoạt động
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i data-lucide="x-circle" class="h-3 w-3 mr-1"></i>
                                                Tạm đóng
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="space-y-1 text-sm text-muted-foreground">
                                        @if(isset($theater['address']))
                                            <div class="flex items-center">
                                                <i data-lucide="map-pin" class="h-4 w-4 mr-2"></i>
                                                {{ $theater['address'] }}
                                            </div>
                                        @endif
                                        
                                        @if(isset($theater['phone']))
                                            <div class="flex items-center">
                                                <i data-lucide="phone" class="h-4 w-4 mr-2"></i>
                                                {{ $theater['phone'] }}
                                            </div>
                                        @endif
                                        
                                        <div class="flex items-center">
                                            <i data-lucide="monitor" class="h-4 w-4 mr-2"></i>
                                            {{ count($theater['rooms'] ?? []) }} phòng chiếu
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button onclick="viewTheater({{ $theater['id'] ?? 0 }})" 
                                            class="text-sm text-primary hover:text-primary/80"
                                            title="Xem chi tiết">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </button>
                                    <button onclick="editTheater({{ $theater['id'] ?? 0 }})" 
                                            class="text-sm text-muted-foreground hover:text-foreground"
                                            title="Chỉnh sửa">
                                        <i data-lucide="edit" class="h-4 w-4"></i>
                                    </button>
                                    <button onclick="manageRooms({{ $theater['id'] ?? 0 }})" 
                                            class="text-sm text-blue-600 hover:text-blue-700"
                                            title="Tạo suất chiếu mới">
                                        <i data-lucide="calendar-plus" class="h-4 w-4"></i>
                                    </button>
                                    <button onclick="deleteTheater({{ $theater['id'] ?? 0 }}, '{{ $theater['name'] ?? 'N/A' }}')" 
                                            class="text-sm text-red-600 hover:text-red-700"
                                            title="Xóa rạp">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Rooms -->
                            @if(isset($theater['rooms']) && count($theater['rooms']) > 0)
                                <div class="border-t border-border pt-4">
                                    <h4 class="text-sm font-medium mb-3">Phòng chiếu</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($theater['rooms'] as $room)
                                            <div class="bg-muted rounded-lg p-3">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="font-medium text-sm">{{ $room['name'] ?? 'N/A' }}</span>
                                                    @if($room['is_active'] ?? true)
                                                        <span class="text-xs text-green-600">Hoạt động</span>
                                                    @else
                                                        <span class="text-xs text-red-600">Tạm đóng</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-muted-foreground space-y-1">
                                                    <div>{{ $room['seat_count'] ?? 0 }} ghế</div>
                                                    <div>Loại: {{ $room['type'] ?? 'Standard' }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <i data-lucide="building-2" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-foreground mb-2">Chưa có rạp chiếu nào</h3>
                        <p class="text-muted-foreground">Hệ thống chưa có rạp chiếu nào được thêm vào.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewTheater(theaterId) {
    window.location.href = `/admin/theaters/${theaterId}`;
}

function editTheater(theaterId) {
    window.location.href = `/admin/theaters/${theaterId}/edit`;
}

function manageRooms(theaterId) {
    // Redirect to create schedule page for this theater
    window.location.href = `/admin/theaters/${theaterId}/schedules/create`;
}

function deleteTheater(theaterId, theaterName) {
    showDeleteConfirmation(theaterId, theaterName);
}

function showDeleteConfirmation(theaterId, theaterName) {
    // Create modal overlay
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    overlay.id = 'delete-modal-overlay';
    
    // Create modal content
    const modal = document.createElement('div');
    modal.className = 'bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all';
    modal.innerHTML = `
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-10 h-10 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600"></i>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Xác nhận xóa rạp chiếu</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Bạn có chắc chắn muốn xóa rạp chiếu <strong>"${theaterName}"</strong>?<br>
                    <span class="text-red-600 font-medium">Hành động này không thể hoàn tác.</span>
                </p>
                <div class="flex gap-3 justify-center">
                    <button onclick="closeDeleteModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Hủy
                    </button>
                    <button onclick="confirmDelete(${theaterId})" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Xóa rạp
                    </button>
                </div>
            </div>
        </div>
    `;
    
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    
    // Close modal when clicking overlay
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeDeleteModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
}

function closeDeleteModal() {
    const overlay = document.getElementById('delete-modal-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function confirmDelete(theaterId) {
    // Create a form to submit DELETE request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/theaters/${theaterId}`;
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    
    // Add method override for DELETE
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection
