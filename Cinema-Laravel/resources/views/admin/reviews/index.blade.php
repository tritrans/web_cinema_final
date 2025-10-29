@extends('layouts.admin')

@section('title', 'Quản lý đánh giá')

@php
    $breadcrumb = [
        ['title' => 'Quản lý đánh giá']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Quản lý đánh giá</h1>
            <p class="text-muted-foreground">Quản lý đánh giá và xếp hạng phim từ người dùng</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Filter buttons -->
            <div class="flex items-center gap-2">
                <button onclick="filterReviews('all')" 
                        class="filter-btn active px-3 py-1 text-sm rounded-full border border-gray-300 bg-blue-100 text-blue-800">
                    Tất cả
                </button>
                <button onclick="filterReviews('high')" 
                        class="filter-btn px-3 py-1 text-sm rounded-full border border-gray-300 hover:bg-gray-100">
                    Đánh giá cao (4-5⭐)
                </button>
                <button onclick="filterReviews('low')" 
                        class="filter-btn px-3 py-1 text-sm rounded-full border border-gray-300 hover:bg-gray-100">
                    Đánh giá thấp (1-2⭐)
                </button>
            </div>
            <button onclick="loadReviews()" 
                    class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                Làm mới
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Tổng đánh giá</h3>
                <i data-lucide="star" class="h-4 w-4 text-yellow-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ count($reviews) }}</div>
                <p class="text-xs text-muted-foreground">Tất cả đánh giá</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Đánh giá cao</h3>
                <i data-lucide="thumbs-up" class="h-4 w-4 text-green-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ count(array_filter($reviews, fn($r) => ($r['rating'] ?? 0) >= 4)) }}</div>
                <p class="text-xs text-muted-foreground">4-5 sao</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Đánh giá thấp</h3>
                <i data-lucide="thumbs-down" class="h-4 w-4 text-red-600"></i>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold">{{ count(array_filter($reviews, fn($r) => ($r['rating'] ?? 0) <= 2)) }}</div>
                <p class="text-xs text-muted-foreground">1-2 sao</p>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <h3 class="text-sm font-medium">Trung bình</h3>
                <i data-lucide="trending-up" class="h-4 w-4 text-blue-600"></i>
            </div>
            <div class="space-y-1">
                @php
                    $avgRating = count($reviews) > 0 ? array_sum(array_map(fn($r) => $r['rating'] ?? 0, $reviews)) / count($reviews) : 0;
                @endphp
                <div class="text-2xl font-bold">{{ number_format($avgRating, 1) }}</div>
                <p class="text-xs text-muted-foreground">Điểm trung bình</p>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="bg-card border border-border rounded-lg shadow-sm p-6" style="display: none;">
        <div class="flex items-center justify-center py-12">
            <div class="flex items-center gap-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-muted-foreground">Đang tải dữ liệu...</span>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div id="reviews-container" class="bg-card border border-border rounded-lg shadow-sm">
        <div class="p-6">
            @if(count($reviews) > 0)
                <div id="reviews-list" class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="review-item border border-border rounded-lg p-4" data-rating="{{ $review['rating'] ?? 0 }}">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="flex items-center gap-2">
                                            @php
                                                $avatarUrl = $review['user_avatar_url'] ?? null;
                                                if ($avatarUrl && !str_starts_with($avatarUrl, 'http')) {
                                                    $avatarUrl = url('storage/' . $avatarUrl);
                                                }
                                            @endphp
                                            @if($avatarUrl)
                                                <img src="{{ $avatarUrl }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center" style="display: none;">
                                                    <span class="text-primary-foreground text-sm font-medium">
                                                        {{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                                                    <span class="text-primary-foreground text-sm font-medium">
                                                        {{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="font-medium text-gray-900">{{ $review['user_name'] ?? $review['name'] ?? 'Người dùng ẩn danh' }}</h4>
                                                <p class="text-xs text-gray-500">{{ $review['user_email'] ?? $review['email'] ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center ml-auto">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= ($review['rating'] ?? 0))
                                                    <i data-lucide="star" class="h-4 w-4 text-yellow-500 fill-current"></i>
                                                @else
                                                    <i data-lucide="star" class="h-4 w-4 text-gray-300"></i>
                                                @endif
                                            @endfor
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $review['rating'] ?? 0 }}/5</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Movie Information -->
                                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                        <div class="flex items-center gap-3">
                                            @if(isset($review['movie_poster']) && $review['movie_poster'])
                                                <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($review['movie_poster'], '/images/placeholder-movie.svg') }}" alt="Movie Poster" class="w-12 h-16 rounded object-cover" onerror="this.src='/images/placeholder-movie.svg'">
                                            @else
                                                <div class="w-12 h-16 rounded bg-gray-200 flex items-center justify-center">
                                                    <i data-lucide="film" class="h-6 w-6 text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $review['movie_title_vi'] ?? $review['movie_title'] ?? 'N/A' }}
                                                </p>
                                                @if(isset($review['movie_title']) && $review['movie_title'] && $review['movie_title'] !== ($review['movie_title_vi'] ?? ''))
                                                    <p class="text-xs text-gray-500">{{ $review['movie_title'] }}</p>
                                                @endif
                                                <p class="text-xs text-blue-600 mt-1">
                                                    <i data-lucide="film" class="h-3 w-3 inline mr-1"></i>
                                                    ID: {{ $review['movie_id'] ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isset($review['is_hidden']) && $review['is_hidden'])
                                        <div class="bg-red-100 border border-red-300 text-red-700 px-3 py-2 rounded-md mb-2">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="eye-off" class="h-4 w-4"></i>
                                                <span class="text-sm font-medium">Nội dung này đã bị ẩn do vi phạm</span>
                                            </div>
                                            @if(isset($review['hidden_reason']))
                                                <p class="text-xs mt-1">Lý do: {{ $review['hidden_reason'] }}</p>
                                            @endif
                                        </div>
                                    @elseif(isset($review['comment']) && $review['comment'])
                                        <p class="text-sm">{{ $review['comment'] }}</p>
                                    @endif
                                </div>
                                <div class="relative">
                                    <button onclick="toggleReviewActions({{ $review['id'] ?? 0 }})" 
                                            class="p-2 text-muted-foreground hover:text-foreground transition-colors border border-border rounded-md hover:bg-accent">
                                        <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div id="reviewActions{{ $review['id'] ?? 0 }}" 
                                         class="hidden absolute right-0 mt-2 w-48 bg-white border border-border rounded-md shadow-lg z-10">
                                        <div class="py-1">
                                            <button onclick="approveReview({{ $review['id'] ?? 0 }})" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                <i data-lucide="check" class="h-4 w-4 mr-2"></i>
                                                Duyệt đánh giá
                                            </button>
                                            <button onclick="reportViolation({{ $review['id'] ?? 0 }})" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-orange-600 hover:bg-orange-50">
                                                <i data-lucide="flag" class="h-4 w-4 mr-2"></i>
                                                Báo cáo vi phạm
                                            </button>
                                            <div class="border-t border-border my-1"></div>
                                            <button onclick="deleteReview({{ $review['id'] ?? 0 }})" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                                                Xóa đánh giá
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {{ isset($review['created_at']) ? \Carbon\Carbon::parse($review['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div id="empty-state" class="text-center py-12">
                    <i data-lucide="star" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-foreground mb-2">Chưa có đánh giá nào</h3>
                    <p class="text-muted-foreground">Hệ thống chưa có đánh giá nào từ người dùng.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentFilter = 'all';
let allReviews = [];

// Helper function to get user avatar URL
function getUserAvatarUrl(avatarUrl) {
    if (!avatarUrl) return null;
    
    // If it's a relative path, prepend storage URL
    if (avatarUrl.startsWith('uploads/') || avatarUrl.startsWith('avatars/')) {
        return `/storage/${avatarUrl}`;
    }
    
    // If it's already a full URL, return as is
    if (avatarUrl.startsWith('http')) {
        return avatarUrl;
    }
    
    // Default fallback
    return null;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Store initial reviews data
    allReviews = @json($reviews);
    console.log('Loaded reviews:', allReviews);
});

// Filter reviews by rating
function filterReviews(filter) {
    currentFilter = filter;
    
    // Update filter button states
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
        btn.classList.add('hover:bg-gray-100');
    });
    
    event.target.classList.add('active', 'bg-blue-100', 'text-blue-800');
    event.target.classList.remove('hover:bg-gray-100');
    
    // Filter reviews
    let filteredReviews = allReviews;
    
    if (filter === 'high') {
        filteredReviews = allReviews.filter(review => (review.rating || 0) >= 4);
    } else if (filter === 'low') {
        filteredReviews = allReviews.filter(review => (review.rating || 0) <= 2);
    }
    
    // Update stats
    updateStats(filteredReviews);
    
    // Render filtered reviews
    renderReviews(filteredReviews);
}

// Update statistics
function updateStats(reviews) {
    const total = reviews.length;
    const high = reviews.filter(r => (r.rating || 0) >= 4).length;
    const low = reviews.filter(r => (r.rating || 0) <= 2).length;
    const avg = total > 0 ? (reviews.reduce((sum, r) => sum + (r.rating || 0), 0) / total).toFixed(1) : 0;
    
    // Update stats cards (you'll need to add IDs to the stat elements)
    console.log('Updated stats:', { total, high, low, avg });
}

// Render reviews list
function renderReviews(reviews) {
    const container = document.getElementById('reviews-list');
    const emptyState = document.getElementById('empty-state');
    
    if (reviews.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }
    
    container.style.display = 'block';
    emptyState.style.display = 'none';
    
    // Clear existing reviews
    container.innerHTML = '';
    
    // Render each review
    reviews.forEach(review => {
        const reviewElement = createReviewElement(review);
        container.appendChild(reviewElement);
    });
}

// Create review element
function createReviewElement(review) {
    const div = document.createElement('div');
    div.className = 'review-item border border-border rounded-lg p-4';
    div.setAttribute('data-rating', review.rating || 0);
    
    const stars = Array.from({length: 5}, (_, i) => 
        `<i data-lucide="star" class="h-4 w-4 ${i < (review.rating || 0) ? 'text-yellow-500 fill-current' : 'text-gray-300'}"></i>`
    ).join('');
    
    const avatarUrl = getUserAvatarUrl(review.user_avatar_url);
    const userAvatar = avatarUrl ? 
        `<img src="${avatarUrl}" alt="Avatar" class="w-8 h-8 rounded-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
         <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center" style="display: none;">
             <span class="text-primary-foreground text-sm font-medium">${(review.user_name || 'U').charAt(0).toUpperCase()}</span>
         </div>` :
        `<div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
             <span class="text-primary-foreground text-sm font-medium">${(review.user_name || 'U').charAt(0).toUpperCase()}</span>
         </div>`;
    
    const moviePoster = review.movie_poster ? 
        `<img src="${review.movie_poster}" alt="Movie Poster" class="w-12 h-16 rounded object-cover" onerror="this.src='/images/placeholder-movie.svg'">` :
        `<div class="w-12 h-16 rounded bg-gray-200 flex items-center justify-center">
            <i data-lucide="film" class="h-6 w-6 text-gray-400"></i>
        </div>`;
    
    const movieTitle = review.movie_title_vi || review.movie_title || 'N/A';
    const movieTitleEn = review.movie_title && review.movie_title !== movieTitle ? 
        `<p class="text-xs text-gray-500">${review.movie_title}</p>` : '';
    
    div.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        ${userAvatar}
                        <div>
                            <h4 class="font-medium text-gray-900">${review.user_name || review.name || 'Người dùng ẩn danh'}</h4>
                            <p class="text-xs text-gray-500">${review.user_email || review.email || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="flex items-center ml-auto">
                        ${stars}
                        <span class="ml-2 text-sm font-medium text-gray-700">${review.rating || 0}/5</span>
                    </div>
                </div>
                
                <!-- Movie Information -->
                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                    <div class="flex items-center gap-3">
                        ${moviePoster}
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${movieTitle}</p>
                            ${movieTitleEn}
                            <p class="text-xs text-blue-600 mt-1">
                                <i data-lucide="film" class="h-3 w-3 inline mr-1"></i>
                                ID: ${review.movie_id || 'N/A'}
                            </p>
                        </div>
                    </div>
                </div>
                
                ${review.comment ? `<p class="text-sm">${review.comment}</p>` : ''}
            </div>
            <div class="relative">
                <button onclick="toggleReviewActions(${review.id || 0})" 
                        class="p-2 text-muted-foreground hover:text-foreground transition-colors border border-border rounded-md hover:bg-accent">
                    <i data-lucide="more-horizontal" class="h-4 w-4"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="reviewActions${review.id || 0}" 
                     class="hidden absolute right-0 mt-2 w-48 bg-white border border-border rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button onclick="approveReview(${review.id || 0})" 
                                class="flex items-center w-full px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                            <i data-lucide="check" class="h-4 w-4 mr-2"></i>
                            Duyệt đánh giá
                        </button>
                        <button onclick="reportViolation(${review.id || 0})" 
                                class="flex items-center w-full px-4 py-2 text-sm text-orange-600 hover:bg-orange-50">
                            <i data-lucide="flag" class="h-4 w-4 mr-2"></i>
                            Báo cáo vi phạm
                        </button>
                        <div class="border-t border-border my-1"></div>
                        <button onclick="deleteReview(${review.id || 0})" 
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i data-lucide="trash-2" class="h-4 w-4 mr-2"></i>
                            Xóa đánh giá
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-xs text-muted-foreground">
            ${review.created_at ? new Date(review.created_at).toLocaleDateString('vi-VN') + ' ' + new Date(review.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'}) : 'N/A'}
        </div>
    `;
    
    return div;
}

// Load reviews via AJAX
function loadReviews() {
    const loadingState = document.getElementById('loading-state');
    const reviewsContainer = document.getElementById('reviews-container');
    
    // Show loading
    loadingState.style.display = 'block';
    reviewsContainer.style.display = 'none';
    
    // Make AJAX request
    fetch('/admin/reviews/load', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            allReviews = data.reviews || [];
            filterReviews(currentFilter); // Re-apply current filter
        } else {
            console.error('Failed to load reviews:', data.message);
            alert('Không thể tải dữ liệu đánh giá: ' + (data.message || 'Lỗi không xác định'));
        }
    })
    .catch(error => {
        console.error('Error loading reviews:', error);
        alert('Có lỗi xảy ra khi tải dữ liệu');
    })
    .finally(() => {
        // Hide loading
        loadingState.style.display = 'none';
        reviewsContainer.style.display = 'block';
    });
}

function approveReview(reviewId) {
    if (confirm('Bạn có chắc chắn muốn duyệt đánh giá này?')) {
        // TODO: Implement approve review API call
        alert('Tính năng duyệt đánh giá sẽ được phát triển. Review ID: ' + reviewId);
    }
}

function reportViolation(reviewId) {
    // Create modal for violation reporting
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 w-96 max-w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Báo cáo vi phạm</h3>
            <form id="violationForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại vi phạm:</label>
                    <select id="violationType" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <textarea id="violationReason" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Mô tả chi tiết về vi phạm..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeViolationModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Báo cáo</button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('violationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const violationType = document.getElementById('violationType').value;
        const reason = document.getElementById('violationReason').value.trim();
        
        if (!reason) {
            alert('Vui lòng nhập lý do báo cáo vi phạm');
            return;
        }
        
        // Debug log
        console.log('Reporting violation for review ID:', reviewId);
        console.log('Violation type:', violationType);
        console.log('Description:', reason);
        
        // Call API to report violation
        fetch('/api/violations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                reportable_id: reviewId,
                reportable_type: 'App\\Models\\Review',
                violation_type: violationType,
                description: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đã báo cáo vi phạm thành công! Báo cáo đang chờ xử lý.');
                closeViolationModal();
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

function closeViolationModal() {
    const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
    if (modal) {
        modal.remove();
    }
}

function deleteReview(reviewId) {
    if (confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
        // TODO: Implement delete review API call
        alert('Tính năng xóa đánh giá sẽ được phát triển. Review ID: ' + reviewId);
    }
}

// Toggle dropdown menu for reviews
function toggleReviewActions(reviewId) {
    const dropdown = document.getElementById('reviewActions' + reviewId);
    const isHidden = dropdown.classList.contains('hidden');
    
    // Close all other dropdowns
    document.querySelectorAll('[id^="reviewActions"]').forEach(d => {
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
        document.querySelectorAll('[id^="reviewActions"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});
</script>
@endpush
@endsection
