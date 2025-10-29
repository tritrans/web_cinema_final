@extends('layouts.admin')

@section('title', 'Quản lý bình luận')

@php
    $breadcrumb = [
        ['title' => 'Quản lý bình luận']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Quản lý bình luận</h1>
            <p class="text-muted-foreground">Quản lý bình luận từ người dùng về phim</p>
        </div>
        <button onclick="window.location.reload()" 
                class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
            <i data-lucide="refresh-cw" class="h-4 w-4"></i>
            Làm mới
        </button>
    </div>

    <!-- Comments List -->
    <div class="bg-card border border-border rounded-lg shadow-sm">
        <div class="p-6">
            @if(count($comments) > 0)
                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="border border-border rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        @php
                                            $avatarUrl = $comment['user_avatar_url'] ?? null;
                                            if ($avatarUrl && !str_starts_with($avatarUrl, 'http')) {
                                                $avatarUrl = url('storage/' . $avatarUrl);
                                            }
                                        @endphp
                                        @if($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center" style="display: none;">
                                                <span class="text-primary-foreground text-sm font-medium">
                                                    {{ strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                                                <span class="text-primary-foreground text-sm font-medium">
                                                    {{ strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="font-medium">{{ $comment['user_name'] ?? 'Người dùng ẩn danh' }}</h4>
                                            <p class="text-xs text-muted-foreground">{{ $comment['user_email'] ?? 'N/A' }}</p>
                                        </div>
                                        <span class="text-xs text-muted-foreground">
                                            {{ isset($comment['created_at']) ? \Carbon\Carbon::parse($comment['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-muted-foreground mb-2">
                                        Phim: <span class="font-medium">{{ $comment['movie_title'] ?? 'N/A' }}</span>
                                    </p>
                                    @if(isset($comment['is_hidden']) && $comment['is_hidden'])
                                        <div class="bg-red-100 border border-red-300 text-red-700 px-3 py-2 rounded-md mb-2">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="eye-off" class="h-4 w-4"></i>
                                                <span class="text-sm font-medium">Nội dung này đã bị ẩn do vi phạm</span>
                                            </div>
                                            @if(isset($comment['hidden_reason']))
                                                <p class="text-xs mt-1">Lý do: {{ $comment['hidden_reason'] }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm">{{ $comment['content'] ?? 'N/A' }}</p>
                                    @endif
                                </div>
                                <div class="relative">
                                    <button onclick="toggleCommentActions({{ $comment['id'] ?? 0 }})" 
                                            class="p-2 text-muted-foreground hover:text-foreground transition-colors border border-border rounded-md hover:bg-accent">
                                        <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div id="commentActions{{ $comment['id'] ?? 0 }}" 
                                         class="hidden absolute right-0 mt-2 w-48 bg-white border border-border rounded-md shadow-lg z-10">
                                        <div class="py-1">
                                            <button onclick="approveComment({{ $comment['id'] ?? 0 }})" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                <i data-lucide="check" class="h-4 w-4 mr-2"></i>
                                                Duyệt bình luận
                                            </button>
                                            <button onclick="reportCommentViolation({{ $comment['id'] ?? 0 }})" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-orange-600 hover:bg-orange-50">
                                                <i data-lucide="flag" class="h-4 w-4 mr-2"></i>
                                                Báo cáo vi phạm
                                            </button>
                                            <div class="border-t border-border my-1"></div>
                                            <button onclick="deleteComment({{ $comment['id'] ?? 0 }})" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                                                Xóa bình luận
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="message-square" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-foreground mb-2">Chưa có bình luận nào</h3>
                    <p class="text-muted-foreground">Hệ thống chưa có bình luận nào từ người dùng.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle dropdown menu for comments
function toggleCommentActions(commentId) {
    const dropdown = document.getElementById('commentActions' + commentId);
    const isHidden = dropdown.classList.contains('hidden');
    
    // Close all other dropdowns
    document.querySelectorAll('[id^="commentActions"]').forEach(d => {
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
        document.querySelectorAll('[id^="commentActions"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});

// Approve comment
function approveComment(commentId) {
    if (confirm('Bạn có chắc chắn muốn duyệt bình luận này?')) {
        fetch(`/api/comments/${commentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'approved' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Duyệt bình luận thành công!');
                location.reload();
            } else {
                alert('Lỗi khi duyệt bình luận: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error approving comment:', error);
            alert('Lỗi khi duyệt bình luận: ' + error.message);
        });
    }
}

// Report comment violation
function reportCommentViolation(commentId) {
    // Create modal for violation reporting
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Báo cáo vi phạm</h3>
            <form id="commentViolationForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại vi phạm:</label>
                    <select id="commentViolationType" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="inappropriate_content">Nội dung không phù hợp</option>
                        <option value="spam">Spam</option>
                        <option value="harassment">Quấy rối</option>
                        <option value="fake_review">Đánh giá giả</option>
                        <option value="offensive_language">Ngôn ngữ xúc phạm</option>
                        <option value="copyright_violation">Vi phạm bản quyền</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lý do chi tiết:</label>
                    <textarea id="commentViolationReason" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Mô tả chi tiết về vi phạm..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeCommentViolationModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Báo cáo</button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('commentViolationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const violationType = document.getElementById('commentViolationType').value;
        const reason = document.getElementById('commentViolationReason').value.trim();
        
        if (!reason) {
            alert('Vui lòng nhập lý do báo cáo vi phạm');
            return;
        }
        
        // Debug log
        console.log('Reporting violation for comment ID:', commentId);
        console.log('Violation type:', violationType);
        console.log('Description:', reason);
        
        fetch('/api/violations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                reportable_type: 'App\\Models\\Comment',
                reportable_id: commentId,
                violation_type: violationType,
                description: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Báo cáo vi phạm thành công! Báo cáo đang chờ xử lý.');
                closeCommentViolationModal();
            } else {
                alert('Lỗi khi báo cáo vi phạm: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error reporting violation:', error);
            alert('Lỗi khi báo cáo vi phạm: ' + error.message);
        });
    });
}

function closeCommentViolationModal() {
    const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
    if (modal) {
        modal.remove();
    }
}


// Delete comment
function deleteComment(commentId) {
    if (confirm('Bạn có chắc chắn muốn xóa bình luận này? Hành động này không thể hoàn tác.')) {
        fetch(`/api/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Xóa bình luận thành công!');
                location.reload();
            } else {
                alert('Lỗi khi xóa bình luận: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error deleting comment:', error);
            alert('Lỗi khi xóa bình luận: ' + error.message);
        });
    }
}
</script>
@endpush
@endsection
