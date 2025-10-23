@extends('layouts.admin')

@section('title', 'Quản lý vi phạm')

@php
    $breadcrumb = [
        ['title' => 'Quản lý vi phạm']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Quản lý vi phạm</h1>
            <p class="text-muted-foreground">Quản lý các báo cáo vi phạm và xử lý nội dung không phù hợp</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="loadViolations()" 
                class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
            <i data-lucide="refresh-cw" class="h-4 w-4"></i>
            Làm mới
        </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $totalViolations = count($violations);
            $pendingViolations = count(array_filter($violations, fn($v) => ($v['status'] ?? '') === 'pending'));
            $resolvedViolations = count(array_filter($violations, fn($v) => ($v['status'] ?? '') === 'resolved'));
            $reviewViolations = count(array_filter($violations, fn($v) => ($v['reportable_type'] ?? '') === 'App\\Models\\Review'));
        @endphp

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Tổng báo cáo</h3>
                <i data-lucide="flag" class="h-4 w-4 text-red-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($totalViolations) }}</div>
                <p class="text-xs text-muted-foreground">Tất cả báo cáo</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Chờ xử lý</h3>
                <i data-lucide="clock" class="h-4 w-4 text-yellow-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($pendingViolations) }}</div>
                <p class="text-xs text-muted-foreground">Cần xem xét</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Đã xử lý</h3>
                <i data-lucide="check-circle" class="h-4 w-4 text-green-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($resolvedViolations) }}</div>
                <p class="text-xs text-muted-foreground">Hoàn thành</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Báo cáo đánh giá</h3>
                <i data-lucide="star" class="h-4 w-4 text-blue-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ number_format($reviewViolations) }}</div>
                <p class="text-xs text-muted-foreground">Đánh giá phim</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-card border border-border rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Trạng thái</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending">Chờ xử lý</option>
                    <option value="reviewing">Đang xem xét</option>
                    <option value="resolved">Đã xử lý</option>
                    <option value="dismissed">Đã bỏ qua</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Loại vi phạm</label>
                <select id="typeFilter" class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                    <option value="">Tất cả loại</option>
                    <option value="spam">Spam</option>
                    <option value="inappropriate_content">Nội dung không phù hợp</option>
                    <option value="harassment">Quấy rối</option>
                    <option value="fake_review">Đánh giá giả</option>
                    <option value="offensive_language">Ngôn ngữ xúc phạm</option>
                    <option value="copyright_violation">Vi phạm bản quyền</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Loại nội dung</label>
                <select id="contentFilter" class="w-full px-3 py-2 border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                    <option value="">Tất cả nội dung</option>
                    <option value="App\\Models\\Review">Đánh giá phim</option>
                    <option value="App\\Models\\Comment">Bình luận</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Violations Table -->
    <div class="bg-card border border-border rounded-lg shadow-sm">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-3 px-4 font-medium text-sm">Người báo cáo</th>
                            <th class="text-left py-3 px-4 font-medium text-sm">Nội dung</th>
                            <th class="text-left py-3 px-4 font-medium text-sm">Loại vi phạm</th>
                            <th class="text-left py-3 px-4 font-medium text-sm">Trạng thái</th>
                            <th class="text-left py-3 px-4 font-medium text-sm">Ngày báo cáo</th>
                            <th class="text-left py-3 px-4 font-medium text-sm">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="violationsTableBody">
            @if(count($violations) > 0)
                    @foreach($violations as $violation)
                                <tr class="violation-row border-b border-border hover:bg-muted/50"
                                    data-status="{{ $violation['status'] ?? 'pending' }}"
                                    data-type="{{ $violation['violation_type'] ?? '' }}"
                                    data-content-type="{{ $violation['reportable_type'] ?? '' }}"
                                    data-violation-id="{{ $violation['id'] ?? 0 }}">
                                    
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $reporterAvatar = $violation['reporter']['avatar'] ?? null;
                                                if ($reporterAvatar && !str_starts_with($reporterAvatar, 'http')) {
                                                    $reporterAvatar = url('storage/' . $reporterAvatar);
                                                }
                                                $reporterName = $violation['reporter']['name'] ?? 'Người dùng ẩn danh';
                                            @endphp
                                            @if($reporterAvatar)
                                                <img src="{{ $reporterAvatar }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center" style="display: none;">
                                                    <span class="text-primary-foreground text-sm font-medium">
                                                        {{ strtoupper(substr($reporterName, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                                                    <span class="text-primary-foreground text-sm font-medium">
                                                        {{ strtoupper(substr($reporterName, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-sm">{{ $reporterName }}</div>
                                                <div class="text-xs text-muted-foreground">{{ $violation['reporter']['email'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="py-3 px-4" data-content-id="{{ $violation['reportable_id'] ?? 'N/A' }}">
                                        <div class="text-sm">
                                            @if(($violation['reportable_type'] ?? '') === 'App\\Models\\Review')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i data-lucide="star" class="h-3 w-3 mr-1"></i>
                                                    Đánh giá phim
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i data-lucide="message-circle" class="h-3 w-3 mr-1"></i>
                                                    Bình luận
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-muted-foreground mt-1">
                                            ID: {{ $violation['reportable_id'] ?? 'N/A' }}
                                        </div>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        @php
                                            $typeLabels = [
                                                'spam' => 'Spam',
                                                'inappropriate_content' => 'Nội dung không phù hợp',
                                                'harassment' => 'Quấy rối',
                                                'fake_review' => 'Đánh giá giả',
                                                'offensive_language' => 'Ngôn ngữ xúc phạm',
                                                'copyright_violation' => 'Vi phạm bản quyền',
                                                'other' => 'Khác'
                                            ];
                                            $type = $violation['violation_type'] ?? 'other';
                                            $typeLabel = $typeLabels[$type] ?? 'Khác';
                                        @endphp
                                        <span class="text-sm">{{ $typeLabel }}</span>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'reviewing' => 'bg-blue-100 text-blue-800',
                                                'resolved' => 'bg-green-100 text-green-800',
                                                'dismissed' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Chờ xử lý',
                                                'reviewing' => 'Đang xem xét',
                                                'resolved' => 'Đã xử lý',
                                                'dismissed' => 'Đã bỏ qua'
                                            ];
                                            $status = $violation['status'] ?? 'pending';
                                            $colorClass = $statusColors[$status] ?? $statusColors['pending'];
                                            $statusLabel = $statusLabels[$status] ?? 'Chờ xử lý';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        <div class="text-sm text-muted-foreground">
                                            {{ isset($violation['created_at']) ? \Carbon\Carbon::parse($violation['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                                    </div>
                                    </td>
                                    
                                    <td class="py-3 px-4">
                                        <div class="relative">
                                            <button onclick="toggleViolationActions({{ $violation['id'] ?? 0 }})" 
                                                    class="p-2 text-muted-foreground hover:text-foreground transition-colors border border-border rounded-md hover:bg-accent">
                                                <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                                            </button>
                                            
                                            <!-- Dropdown Menu -->
                                            <div id="violationActions{{ $violation['id'] ?? 0 }}" 
                                                 class="hidden absolute right-0 mt-2 w-48 bg-white border border-border rounded-md shadow-lg z-10">
                                                <div class="py-1">
                                                    <button onclick="viewViolation({{ $violation['id'] ?? 0 }})" 
                                                            class="flex items-center w-full px-4 py-2 text-sm text-foreground hover:bg-muted">
                                                        <i data-lucide="eye" class="h-4 w-4 mr-2"></i>
                                                        Xem chi tiết
                                                    </button>
                                                    
                                                    <div class="border-t border-border my-1"></div>
                                                    @if(($violation['is_hidden'] ?? false))
                                                        <button onclick="toggleContentVisibility({{ $violation['id'] ?? 0 }}, false)" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                            <i data-lucide="eye" class="h-4 w-4 mr-2"></i>
                                                            Hủy ẩn
                                    </button>
                                                    @else
                                                        <button onclick="toggleContentVisibility({{ $violation['id'] ?? 0 }}, true)" 
                                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                            <i data-lucide="eye-off" class="h-4 w-4 mr-2"></i>
                                                            Ẩn nội dung
                                    </button>
                                                    @endif
                                </div>
                            </div>
                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Detail row (hidden by default) -->
                                <tr id="violationDetail{{ $violation['id'] ?? 0 }}" class="hidden bg-gray-50">
                                    <td colspan="6" class="px-4 py-6">
                                        <div class="bg-white rounded-lg border border-border p-4">
                                            <h4 class="font-medium text-lg mb-4 flex items-center gap-2">
                                                <i data-lucide="info" class="h-5 w-5 text-blue-600"></i>
                                                Chi tiết báo cáo vi phạm
                                            </h4>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <!-- Reporter Info -->
                                                <div>
                                                    <h5 class="font-medium text-sm text-gray-700 mb-2">Thông tin người báo cáo</h5>
                                                    <div class="space-y-2">
                                                        <p><span class="font-medium">Tên:</span> {{ $violation['reporter']['name'] ?? 'N/A' }}</p>
                                                        <p><span class="font-medium">Email:</span> {{ $violation['reporter']['email'] ?? 'N/A' }}</p>
                                                        <p><span class="font-medium">Ngày báo cáo:</span> {{ isset($violation['created_at']) ? \Carbon\Carbon::parse($violation['created_at'])->format('d/m/Y H:i') : 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Violation Info -->
                                                <div>
                                                    <h5 class="font-medium text-sm text-gray-700 mb-2">Thông tin vi phạm</h5>
                                                    <div class="space-y-2">
                                                        <p><span class="font-medium">Loại vi phạm:</span> 
                                                            @php
                                                                $typeLabels = [
                                                                    'spam' => 'Spam',
                                                                    'inappropriate_content' => 'Nội dung không phù hợp',
                                                                    'harassment' => 'Quấy rối',
                                                                    'fake_review' => 'Đánh giá giả',
                                                                    'offensive_language' => 'Ngôn ngữ xúc phạm',
                                                                    'copyright_violation' => 'Vi phạm bản quyền',
                                                                    'other' => 'Khác'
                                                                ];
                                                                $type = $violation['violation_type'] ?? 'other';
                                                                $typeLabel = $typeLabels[$type] ?? 'Khác';
                                                            @endphp
                                                            {{ $typeLabel }}
                                                        </p>
                                                        <p><span class="font-medium">Loại nội dung:</span> 
                                                            @if(($violation['reportable_type'] ?? '') === 'App\\Models\\Review')
                                                                Đánh giá phim
                                                            @else
                                                                Bình luận
                                                            @endif
                                                        </p>
                                                        <p><span class="font-medium">ID nội dung:</span> {{ $violation['reportable_id'] ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Description -->
                                            @if(isset($violation['description']) && $violation['description'])
                                                <div class="mt-4">
                                                    <h5 class="font-medium text-sm text-gray-700 mb-2">Mô tả chi tiết</h5>
                                                    <div class="bg-gray-100 rounded-md p-3">
                                                        <p class="text-sm">{{ $violation['description'] }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Content Preview -->
                                            <div class="mt-4">
                                                <h5 class="font-medium text-sm text-gray-700 mb-2">Nội dung bị báo cáo</h5>
                                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                                    <div id="contentPreview{{ $violation['id'] ?? 0 }}">
                                                        <div class="flex items-center justify-center py-4">
                                                            <i data-lucide="loader" class="h-4 w-4 animate-spin mr-2"></i>
                                                            Đang tải nội dung...
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="mt-4 flex items-center gap-2">
                                                @if(($violation['is_hidden'] ?? false))
                                                    <button onclick="toggleContentVisibility({{ $violation['id'] ?? 0 }}, false)" 
                                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm text-green-600 hover:bg-green-50 border border-green-200 rounded-md">
                                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                                        Hủy ẩn nội dung
                                                    </button>
                                                @else
                                                    <button onclick="toggleContentVisibility({{ $violation['id'] ?? 0 }}, true)" 
                                                            class="inline-flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 border border-red-200 rounded-md">
                                                        <i data-lucide="eye-off" class="h-4 w-4"></i>
                                                        Ẩn nội dung
                                                    </button>
                                                @endif
                                                <button onclick="closeViolationDetail({{ $violation['id'] ?? 0 }})" 
                                                        class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-md">
                                                    <i data-lucide="x" class="h-4 w-4"></i>
                                                    Đóng
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                    @endforeach
            @else
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <i data-lucide="flag" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                                    <h3 class="text-lg font-medium text-foreground mb-2">Chưa có báo cáo vi phạm nào</h3>
                                    <p class="text-muted-foreground">Hệ thống chưa nhận được báo cáo vi phạm nào.</p>
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
let allViolations = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Store initial violations data
    allViolations = @json($violations);
    console.log('Loaded violations:', allViolations);
});

// Filter violations
function filterViolations() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const contentFilter = document.getElementById('contentFilter').value;
    
    const violationRows = document.querySelectorAll('.violation-row');
    
    violationRows.forEach(row => {
        const status = row.dataset.status;
        const type = row.dataset.type;
        const contentType = row.dataset.contentType;
        
        let show = true;
        
        if (statusFilter && status !== statusFilter) show = false;
        if (typeFilter && type !== typeFilter) show = false;
        if (contentFilter && contentType !== contentFilter) show = false;
        
        row.style.display = show ? 'table-row' : 'none';
    });
}

// Add event listeners
document.getElementById('statusFilter').addEventListener('change', filterViolations);
document.getElementById('typeFilter').addEventListener('change', filterViolations);
document.getElementById('contentFilter').addEventListener('change', filterViolations);

// View violation details
function viewViolation(violationId) {
    // Close all other detail rows
    document.querySelectorAll('[id^="violationDetail"]').forEach(row => {
        row.classList.add('hidden');
    });
    
    // Toggle current detail row
    const detailRow = document.getElementById('violationDetail' + violationId);
    if (detailRow) {
        detailRow.classList.toggle('hidden');
        
        // Load content preview if showing
        if (!detailRow.classList.contains('hidden')) {
            loadContentPreview(violationId);
        }
    }
    
    // Close dropdown
    document.getElementById('violationActions' + violationId).classList.add('hidden');
}

// Handle violation (change status)
function handleViolation(violationId, status) {
    const statusLabels = {
        'reviewing': 'Đang xem xét',
        'resolved': 'Đã xử lý',
        'dismissed': 'Đã bỏ qua'
    };
    
    if (confirm(`Bạn có chắc chắn muốn chuyển trạng thái sang "${statusLabels[status]}"?`)) {
        // Call API to update violation status
        fetch(`/api/violations/${violationId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: status,
                resolution_notes: `Chuyển trạng thái sang ${statusLabels[status]}`
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cập nhật trạng thái thành công!');
                location.reload();
            } else {
                alert('Lỗi khi cập nhật trạng thái: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating violation:', error);
            alert('Lỗi khi cập nhật trạng thái: ' + error.message);
        });
    }
}

// Toggle dropdown menu
function toggleViolationActions(violationId) {
    const dropdown = document.getElementById('violationActions' + violationId);
    const isHidden = dropdown.classList.contains('hidden');
    
    // Close all other dropdowns
    document.querySelectorAll('[id^="violationActions"]').forEach(d => {
        d.classList.add('hidden');
    });
    
    // Toggle current dropdown
    if (isHidden) {
        dropdown.classList.remove('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        document.querySelectorAll('[id^="violationActions"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Load content preview
function loadContentPreview(violationId) {
    const previewDiv = document.getElementById('contentPreview' + violationId);
    if (!previewDiv) return;
    
    // Get violation data
    const violationRow = document.querySelector(`[data-violation-id="${violationId}"]`);
    if (!violationRow) return;
    
    const contentType = violationRow.dataset.contentType;
    const contentId = violationRow.querySelector('[data-content-id]')?.dataset.contentId;
    
    if (contentType === 'App\\Models\\Review') {
        // Load review content
        fetch(`http://127.0.0.1:8000/api/reviews/${contentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    const review = data.data;
                    previewDiv.innerHTML = `
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Đánh giá phim:</span>
                                <div class="flex items-center gap-1">
                                    ${Array.from({length: 5}, (_, i) => 
                                        `<i data-lucide="star" class="h-4 w-4 ${i < (review.rating || 0) ? 'text-yellow-500 fill-current' : 'text-gray-300'}"></i>`
                                    ).join('')}
                                    <span class="ml-1 text-sm">${review.rating || 0}/5</span>
                                </div>
                            </div>
                            ${review.comment ? `<div class="bg-white rounded-md p-3 border"><p class="text-sm">${review.comment}</p></div>` : ''}
                            <div class="text-xs text-gray-500">
                                Tạo lúc: ${review.created_at ? new Date(review.created_at).toLocaleString('vi-VN') : 'N/A'}
                            </div>
                        </div>
                    `;
                } else {
                    previewDiv.innerHTML = `<p class="text-sm text-gray-500">Không thể tải nội dung đánh giá: ${data.message || 'Unknown error'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error loading review:', error);
                previewDiv.innerHTML = `<p class="text-sm text-red-500">Lỗi khi tải nội dung: ${error.message}</p>`;
            });
    } else {
        // Load comment content
        fetch(`http://127.0.0.1:8000/api/comments/${contentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    const comment = data.data;
                    previewDiv.innerHTML = `
                        <div class="space-y-3">
                            <div class="bg-white rounded-md p-3 border">
                                <p class="text-sm">${comment.content || 'N/A'}</p>
                            </div>
                            <div class="text-xs text-gray-500">
                                Tạo lúc: ${comment.created_at ? new Date(comment.created_at).toLocaleString('vi-VN') : 'N/A'}
                            </div>
                        </div>
                    `;
                } else {
                    previewDiv.innerHTML = `<p class="text-sm text-gray-500">Không thể tải nội dung bình luận: ${data.message || 'Unknown error'}</p>`;
                }
            })
            .catch(error => {
                console.error('Error loading comment:', error);
                previewDiv.innerHTML = `<p class="text-sm text-red-500">Lỗi khi tải nội dung: ${error.message}</p>`;
            });
    }
}

// Toggle content visibility
function toggleContentVisibility(violationId, hide) {
    const action = hide ? 'ẩn' : 'hiện';
    if (confirm(`Bạn có chắc chắn muốn ${action} nội dung này?`)) {
        // Call API to toggle visibility
        fetch(`http://127.0.0.1:8000/api/violations/${violationId}/toggle-visibility`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ hide: hide })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${action.charAt(0).toUpperCase() + action.slice(1)} nội dung thành công!`);
                location.reload();
            } else {
                alert('Lỗi khi cập nhật: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error toggling visibility:', error);
            alert('Lỗi khi cập nhật: ' + error.message);
        });
    }
}

// Close violation detail
function closeViolationDetail(violationId) {
    const detailRow = document.getElementById('violationDetail' + violationId);
    if (detailRow) {
        detailRow.classList.add('hidden');
    }
}

// Load violations
function loadViolations() {
    location.reload();
}
</script>
@endpush
@endsection