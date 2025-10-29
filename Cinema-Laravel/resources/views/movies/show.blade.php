@extends('layouts.app')

@section('title', ($movie['title'] ?? 'Phim') . ' - Phim Việt')
@section('description', $movie['description'] ?? 'Xem phim chất lượng cao')

@section('content')
<div class="min-h-screen bg-background">
    <!-- Hero Section -->
    <section class="relative h-[60vh] min-h-[400px] overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($movie['backdrop'] ?? $movie['poster']) }}" 
                 alt="{{ $movie['title'] }}" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-black/20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
        </div>

        <div class="relative h-full flex items-end">
            <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 w-full">
                <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 w-full">
                    <!-- Movie Poster -->
                    <div class="flex-shrink-0 mx-auto lg:mx-0">
                        <div class="relative w-48 h-72 lg:w-56 lg:h-80 rounded-lg overflow-hidden shadow-2xl">
                            <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($movie['poster']) }}" 
                                 alt="{{ $movie['title'] }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='/images/placeholder-movie.svg'">
                        </div>
                    </div>

                    <!-- Movie Info -->
                    <div class="flex-1 space-y-4 text-white text-center lg:text-left">
                        <div class="space-y-2">
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">{{ $movie['title'] }}</h1>
                            <p class="text-lg lg:text-xl text-white/80">{{ $movie['title_vi'] ?? $movie['title'] }}</p>
                        </div>

                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 text-sm lg:text-base">
                            <div class="flex items-center">
                                <i data-lucide="star" class="h-4 w-4 mr-1 fill-current text-yellow-400"></i>
                                <span class="font-medium">{{ $movie['rating'] ? number_format($movie['rating'], 1) : 'N/A' }}</span>
                                <span class="text-white/60 ml-1">(đánh giá)</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="calendar" class="h-4 w-4 mr-1"></i>
                                <span>{{ date('Y', strtotime($movie['release_date'])) }}</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="clock" class="h-4 w-4 mr-1"></i>
                                <span>{{ $movie['duration'] }} phút</span>
                            </div>
                            <div class="flex items-center">
                                <i data-lucide="globe" class="h-4 w-4 mr-1"></i>
                                <span>{{ $movie['country'] ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap justify-center lg:justify-start gap-2">
                            @if(isset($movie['genre']) && is_array($movie['genre']))
                                @foreach($movie['genre'] as $genre)
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-white/30 text-white">
                                        {{ $genre }}
                                    </span>
                                @endforeach
                            @endif
                        </div>

                        <p class="text-base lg:text-lg text-white/90 leading-relaxed max-w-3xl mx-auto lg:mx-0">
                            {{ $movie['description_vi'] ?? $movie['description'] }}
                        </p>

                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 pt-4">
                            <a href="{{ route('booking.index', ['movie' => $movie['slug'] ?? $movie['id']]) }}" 
                               class="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-md font-medium inline-flex items-center gap-2 w-full sm:w-auto">
                                <i data-lucide="ticket" class="h-5 w-5"></i>
                                Đặt vé
                            </a>
                            <button onclick="handleTrailer()" 
                                    class="border border-white/30 text-white hover:bg-white/10 bg-transparent px-6 py-3 rounded-md font-medium inline-flex items-center gap-2 w-full sm:w-auto">
                                <i data-lucide="play" class="h-5 w-5"></i>
                                Xem trailer
                            </button>
                            <button id="favorite-btn" 
                                    onclick="handleFavorite(this)" 
                                    data-movie-id="{{ $movie['id'] }}"
                                    class="border border-white/30 text-white hover:bg-white/10 bg-transparent px-6 py-3 rounded-md font-medium inline-flex items-center gap-2 w-full sm:w-auto">
                                <i data-lucide="heart" class="h-5 w-5"></i>
                                <span id="favorite-text">Yêu thích</span>
                            </button>
                            <button onclick="handleShare()" 
                                    class="border border-white/30 text-white hover:bg-white/10 bg-transparent px-6 py-3 rounded-md font-medium inline-flex items-center gap-2 w-full sm:w-auto">
                                <i data-lucide="share-2" class="h-5 w-5"></i>
                                Chia sẻ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bg-background">
        <div class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 lg:gap-8">
                <!-- Main Content -->
                <div class="xl:col-span-3 space-y-6 lg:space-y-8">
                    <!-- Movie Details -->
                    <div class="bg-card border rounded-lg shadow-sm">
                        <div class="p-6">
                            <div class="pb-4">
                                <h2 class="text-xl lg:text-2xl font-bold text-foreground">Thông tin phim</h2>
                            </div>
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="font-semibold text-muted-foreground mb-3">Đạo diễn</h4>
                                        <p class="flex items-center text-base">
                                            <i data-lucide="user" class="h-4 w-4 mr-2 text-primary"></i>
                                            {{ $movie['director'] ?? 'Chưa cập nhật' }}
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-muted-foreground mb-3">Ngôn ngữ</h4>
                                        <p class="text-base">{{ $movie['language'] ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-muted-foreground mb-3">Diễn viên</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @if(isset($movie['cast']) && is_array($movie['cast']))
                                            @foreach($movie['cast'] as $actor)
                                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground">
                                                    {{ $actor }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted-foreground">Chưa cập nhật</span>
                                        @endif
                                    </div>
                                </div>

                                <hr class="border-border">

                                <div>
                                    <h4 class="font-semibold text-muted-foreground mb-3">Mô tả chi tiết</h4>
                                    @if(isset($movie['description_vi']) && $movie['description_vi'])
                                        <div class="mb-4">
                                            <h5 class="font-medium text-sm text-muted-foreground mb-2">Tiếng Việt:</h5>
                                            <p class="text-muted-foreground leading-relaxed text-base">{{ $movie['description_vi'] }}</p>
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="font-medium text-sm text-muted-foreground mb-2">English:</h5>
                                        <p class="text-muted-foreground leading-relaxed text-base">{{ $movie['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="bg-card border rounded-lg shadow-sm">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-foreground mb-4">Đánh giá phim</h2>
                            
                            <!-- Add Review Form -->
                            @if(session('user'))
                                <div class="mb-6 p-4 bg-muted/50 rounded-lg">
                                    <h3 class="font-semibold mb-3">Viết đánh giá</h3>
                                    <form id="review-form" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-medium mb-2">Đánh giá của bạn</label>
                                            <div class="flex items-center gap-1" id="rating-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button" onclick="setRating({{ $i }})" 
                                                            class="star-rating h-6 w-6 text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none" 
                                                            data-rating="{{ $i }}">
                                                        <i data-lucide="star" class="h-full w-full"></i>
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="rating-input" value="0">
                                        </div>
                                        <div>
                                            <textarea name="comment" 
                                                      placeholder="Chia sẻ suy nghĩ của bạn về bộ phim này..." 
                                                      class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" 
                                                      rows="3" required></textarea>
                                        </div>
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 font-medium">
                                            <i data-lucide="star" class="h-4 w-4"></i>
                                            Gửi đánh giá
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="mb-6 p-4 bg-muted/50 rounded-lg text-center">
                                    <p class="text-muted-foreground mb-3">Bạn cần đăng nhập để viết đánh giá</p>
                                    <a href="{{ route('login') }}" 
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 font-medium">
                                        Đăng nhập
                                    </a>
                                </div>
                            @endif

                            <!-- Reviews List -->
                            @if(isset($reviews) && count($reviews) > 0)
                                <div class="space-y-4" id="reviews-list">
                                    @foreach($reviews as $review)
                                        <div class="border-b border-border pb-4 last:border-b-0">
                                            @if($review['is_hidden'] ?? false)
                                                <div class="flex items-center gap-3 mb-2">
                                                    @if(isset($review['user_avatar_url']) && $review['user_avatar_url'])
                                                        @php
                                                            $avatarUrl = $review['user_avatar_url'];
                                                            // If it's a relative path, prepend the web app URL
                                                            if (strpos($avatarUrl, 'http') !== 0) {
                                                                $avatarUrl = url('storage/' . $avatarUrl);
                                                            }
                                                        @endphp
                                                        <img src="{{ $avatarUrl }}" 
                                                             alt="{{ $review['user_name'] ?? 'User' }}" 
                                                             class="w-8 h-8 rounded-full object-cover"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-medium" style="display: none;">
                                                            {{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @else
                                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                            {{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium">{{ $review['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                                        <div class="flex items-center gap-1">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i data-lucide="star" class="h-4 w-4 {{ $i <= ($review['rating'] ?? 0) ? 'fill-current text-yellow-400' : 'text-gray-300' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 p-3 bg-red-100 border border-red-200 rounded-md">
                                                    <i data-lucide="eye-off" class="h-4 w-4 text-red-600"></i>
                                                    <span class="text-sm text-red-600 font-medium">Nội dung này đã bị ẩn do vi phạm</span>
                                                </div>
                                                <p class="text-xs text-muted-foreground mt-2">{{ \Carbon\Carbon::parse($review['created_at'])->diffForHumans() }}</p>
                                            @else
                                                <div class="flex items-center gap-3 mb-2">
                                                    @if(isset($review['user_avatar_url']) && $review['user_avatar_url'])
                                                        <img src="{{ $review['user_avatar_url'] }}" 
                                                             alt="{{ $review['user_name'] ?? 'User' }}" 
                                                             class="w-8 h-8 rounded-full object-cover">
                                                    @else
                                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                            {{ strtoupper(substr($review['user_name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium">{{ $review['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                                        <div class="flex items-center gap-1">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i data-lucide="star" class="h-4 w-4 {{ $i <= ($review['rating'] ?? 0) ? 'fill-current text-yellow-400' : 'text-gray-300' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-muted-foreground">{{ $review['comment'] }}</p>
                                                <p class="text-xs text-muted-foreground mt-2">{{ \Carbon\Carbon::parse($review['created_at'])->diffForHumans() }}</p>
                                            
                                            <!-- Display Replies -->
                                            @if(isset($review['replies']) && count($review['replies']) > 0)
                                                <div class="mt-3 ml-6 space-y-3">
                                                    @foreach($review['replies'] as $reply)
                                                        <div class="border-l-2 border-gray-200 pl-4 {{ ($reply['is_hidden'] ?? false) ? 'opacity-50 bg-red-50 border-red-200' : '' }}">
                                                            @if($reply['is_hidden'] ?? false)
                                                                <div class="flex items-center gap-2 p-2 bg-red-100 border border-red-200 rounded-md">
                                                                    <i data-lucide="eye-off" class="h-4 w-4 text-red-600"></i>
                                                                    <span class="text-sm text-red-600 font-medium">Nội dung này đã bị ẩn do vi phạm</span>
                                                                </div>
                                                            @else
                                                                <div class="flex items-center gap-3 mb-2">
                                                                    @if(isset($reply['user_avatar_url']) && $reply['user_avatar_url'])
                                                                        @php
                                                                            $avatarUrl = $reply['user_avatar_url'];
                                                                            if (strpos($avatarUrl, 'http') !== 0) {
                                                                                $avatarUrl = url('storage/' . $avatarUrl);
                                                                            }
                                                                        @endphp
                                                                        <img src="{{ $avatarUrl }}" 
                                                                             alt="{{ $reply['user_name'] ?? 'User' }}" 
                                                                             class="w-6 h-6 rounded-full object-cover"
                                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                        <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-medium" style="display: none;">
                                                                            {{ strtoupper(substr($reply['user_name'] ?? 'U', 0, 1)) }}
                                                                        </div>
                                                                    @else
                                                                        <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                                            {{ strtoupper(substr($reply['user_name'] ?? 'U', 0, 1)) }}
                                                                        </div>
                                                                    @endif
                                                                    <div>
                                                                        <p class="font-medium text-sm">{{ $reply['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                                                        <p class="text-xs text-muted-foreground">{{ \Carbon\Carbon::parse($reply['created_at'])->diffForHumans() }}</p>
                                                                    </div>
                                                                </div>
                                                                <p class="text-sm text-muted-foreground">{{ $reply['content'] ?? $reply['comment'] ?? '' }}</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <!-- Reply to Review -->
                                            @if(session('user') && !($review['is_hidden'] ?? false))
                                                <div class="mt-3">
                                                    <button onclick="toggleReviewReply({{ $review['id'] }})" 
                                                            class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                        <i data-lucide="reply" class="h-4 w-4"></i>
                                                        Trả lời
                                                    </button>
                                                    
                                                    <!-- Reply Form (Hidden by default) -->
                                                    <div id="review-reply-{{ $review['id'] }}" class="hidden mt-3 p-3 bg-muted/50 rounded-lg">
                                                        <form class="review-reply-form" data-review-id="{{ $review['id'] }}">
                                                            @csrf
                                                            <textarea name="content" 
                                                                      placeholder="Viết phản hồi..." 
                                                                      class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" 
                                                                      rows="2" required></textarea>
                                                            <div class="flex gap-2 mt-2">
                                                                <button type="submit" 
                                                                        class="px-3 py-1 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
                                                                    Gửi phản hồi
                                                                </button>
                                                                <button type="button" 
                                                                        onclick="toggleReviewReply({{ $review['id'] }})"
                                                                        class="px-3 py-1 bg-muted text-muted-foreground rounded-md text-sm hover:bg-muted/80">
                                                                    Hủy
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8" id="no-reviews">
                                    <i data-lucide="star" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                                    <p class="text-muted-foreground">Chưa có đánh giá nào</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comments Section -->
                    <div class="bg-card border rounded-lg shadow-sm">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-foreground mb-4">Bình luận</h2>
                            
                            <!-- Add Comment Form -->
                            @if(session('user'))
                                <div class="mb-6 p-4 bg-muted/50 rounded-lg">
                                    <h3 class="font-semibold mb-3">Viết bình luận</h3>
                                    <form id="comment-form" class="space-y-3">
                                        @csrf
                                        <div>
                                            <textarea name="content" 
                                                      placeholder="Chia sẻ suy nghĩ của bạn..." 
                                                      class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" 
                                                      rows="3" required></textarea>
                                        </div>
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 font-medium">
                                            <i data-lucide="message-circle" class="h-4 w-4"></i>
                                            Gửi bình luận
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="mb-6 p-4 bg-muted/50 rounded-lg text-center">
                                    <p class="text-muted-foreground mb-3">Bạn cần đăng nhập để viết bình luận</p>
                                    <a href="{{ route('login') }}" 
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 font-medium">
                                        Đăng nhập
                                    </a>
                                </div>
                            @endif

                            <!-- Comments List -->
                            @if(isset($comments) && count($comments) > 0)
                                <div class="space-y-4" id="comments-list">
                                    @foreach($comments as $comment)
                                        <div class="border-b border-border pb-4 last:border-b-0">
                                            @if($comment['is_hidden'] ?? false)
                                                <div class="flex items-center gap-3 mb-2">
                                                    @if(isset($comment['user_avatar_url']) && $comment['user_avatar_url'])
                                                        @php
                                                            $avatarUrl = $comment['user_avatar_url'];
                                                            // If it's a relative path, prepend the web app URL
                                                            if (strpos($avatarUrl, 'http') !== 0) {
                                                                $avatarUrl = url('storage/' . $avatarUrl);
                                                            }
                                                        @endphp
                                                        <img src="{{ $avatarUrl }}" 
                                                             alt="{{ $comment['user_name'] ?? 'User' }}" 
                                                             class="w-8 h-8 rounded-full object-cover"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-medium" style="display: none;">
                                                            {{ strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @else
                                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                            {{ strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium">{{ $comment['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                                        <p class="text-xs text-muted-foreground">{{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 p-3 bg-red-100 border border-red-200 rounded-md">
                                                    <i data-lucide="eye-off" class="h-4 w-4 text-red-600"></i>
                                                    <span class="text-sm text-red-600 font-medium">Nội dung này đã bị ẩn do vi phạm</span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-3 mb-2">
                                                    @if(isset($comment['user_avatar_url']) && $comment['user_avatar_url'])
                                                        @php
                                                            $avatarUrl = $comment['user_avatar_url'];
                                                            // If it's a relative path, prepend the web app URL
                                                            if (strpos($avatarUrl, 'http') !== 0) {
                                                                $avatarUrl = url('storage/' . $avatarUrl);
                                                            }
                                                        @endphp
                                                        <img src="{{ $avatarUrl }}" 
                                                             alt="{{ $comment['user_name'] ?? 'User' }}" 
                                                             class="w-8 h-8 rounded-full object-cover"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-medium" style="display: none;">
                                                            {{ strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @else
                                                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                            {{ strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="font-medium">{{ $comment['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                                        <p class="text-xs text-muted-foreground">{{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                                <p class="text-muted-foreground">{{ $comment['content'] }}</p>
                                            
                                            <!-- Display Replies -->
                                            @if(isset($comment['replies']) && count($comment['replies']) > 0)
                                                <div class="mt-3 ml-6 space-y-3">
                                                    @foreach($comment['replies'] as $reply)
                                                        <div class="border-l-2 border-gray-200 pl-4 {{ ($reply['is_hidden'] ?? false) ? 'opacity-50 bg-red-50 border-red-200' : '' }}">
                                                            @if($reply['is_hidden'] ?? false)
                                                                <div class="flex items-center gap-2 p-2 bg-red-100 border border-red-200 rounded-md">
                                                                    <i data-lucide="eye-off" class="h-4 w-4 text-red-600"></i>
                                                                    <span class="text-sm text-red-600 font-medium">Nội dung này đã bị ẩn do vi phạm</span>
                                                                </div>
                                                            @else
                                                                <div class="flex items-center gap-3 mb-2">
                                                                    @if(isset($reply['user_avatar_url']) && $reply['user_avatar_url'])
                                                                        @php
                                                                            $avatarUrl = $reply['user_avatar_url'];
                                                                            if (strpos($avatarUrl, 'http') !== 0) {
                                                                                $avatarUrl = url('storage/' . $avatarUrl);
                                                                            }
                                                                        @endphp
                                                                        <img src="{{ $avatarUrl }}" 
                                                                             alt="{{ $reply['user_name'] ?? 'User' }}" 
                                                                             class="w-6 h-6 rounded-full object-cover"
                                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                        <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-medium" style="display: none;">
                                                                            {{ strtoupper(substr($reply['user_name'] ?? 'U', 0, 1)) }}
                                                                        </div>
                                                                    @else
                                                                        <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                                            {{ strtoupper(substr($reply['user_name'] ?? 'U', 0, 1)) }}
                                                                        </div>
                                                                    @endif
                                                                    <div>
                                                                        <p class="font-medium text-sm">{{ $reply['user_name'] ?? 'Người dùng ẩn danh' }}</p>
                                                                        <p class="text-xs text-muted-foreground">{{ \Carbon\Carbon::parse($reply['created_at'])->diffForHumans() }}</p>
                                                                    </div>
                                                                </div>
                                                                <p class="text-sm text-muted-foreground">{{ $reply['content'] ?? $reply['comment'] ?? '' }}</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <!-- Reply to Comment -->
                                            @if(session('user') && !($comment['is_hidden'] ?? false))
                                                <div class="mt-3">
                                                    <button onclick="toggleCommentReply({{ $comment['id'] }})" 
                                                            class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                                        <i data-lucide="reply" class="h-4 w-4"></i>
                                                        Trả lời
                                                    </button>
                                                    
                                                    <!-- Reply Form (Hidden by default) -->
                                                    <div id="comment-reply-{{ $comment['id'] }}" class="hidden mt-3 p-3 bg-muted/50 rounded-lg">
                                                        <form class="comment-reply-form" data-comment-id="{{ $comment['id'] }}">
                                                            @csrf
                                                            <textarea name="content" 
                                                                      placeholder="Viết phản hồi..." 
                                                                      class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2" 
                                                                      rows="2" required></textarea>
                                                            <div class="flex gap-2 mt-2">
                                                                <button type="submit" 
                                                                        class="px-3 py-1 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
                                                                    Gửi phản hồi
                                                                </button>
                                                                <button type="button" 
                                                                        onclick="toggleCommentReply({{ $comment['id'] }})"
                                                                        class="px-3 py-1 bg-muted text-muted-foreground rounded-md text-sm hover:bg-muted/80">
                                                                    Hủy
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8" id="no-comments">
                                    <i data-lucide="message-circle" class="h-12 w-12 text-muted-foreground mx-auto mb-4"></i>
                                    <p class="text-muted-foreground">Chưa có bình luận nào</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- Movie Stats -->
                    <div class="bg-card border rounded-lg shadow-sm">
                        <div class="p-6">
                            <div class="pb-4">
                                <h3 class="text-lg font-bold text-foreground">Thống kê</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-muted-foreground text-sm">Rating IMDb</span>
                                    <div class="flex items-center">
                                        <i data-lucide="star" class="h-4 w-4 mr-1 fill-current text-yellow-400"></i>
                                        <span class="font-semibold">{{ $movie['rating'] ? number_format($movie['rating'], 1) . '/10' : 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between py-2">
                                    <span class="text-muted-foreground text-sm">Thời lượng</span>
                                    <span class="font-semibold">{{ $movie['duration'] }} phút</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Movies -->
                    @if(isset($relatedMovies) && count($relatedMovies) > 0)
                        <div class="bg-card border rounded-lg shadow-sm">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-foreground mb-4">Phim liên quan</h3>
                                <!-- 2-column responsive grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($relatedMovies as $relatedMovie)
                                        <a href="{{ route('movies.show.slug', $relatedMovie['slug'] ?? $relatedMovie['id']) }}" class="flex gap-3 group">
                                            <div class="w-16 h-20 rounded-lg overflow-hidden flex-shrink-0">
                                                <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($relatedMovie['poster']) }}" 
                                                     alt="{{ $relatedMovie['title'] }}" 
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                                     onerror="this.src='/images/placeholder-movie.svg'">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-sm group-hover:text-primary transition-colors line-clamp-2">{{ $relatedMovie['title'] }}</h4>
                                                <p class="text-xs text-muted-foreground mt-1">{{ date('Y', strtotime($relatedMovie['release_date'])) }}</p>
                                                <div class="flex items-center gap-1 mt-1">
                                                    <i data-lucide="star" class="h-3 w-3 fill-current text-yellow-400"></i>
                                                    <span class="text-xs text-muted-foreground">{{ $relatedMovie['rating'] ? number_format($relatedMovie['rating'], 1) : 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trailer Modal -->
<div id="trailer-modal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
    <div class="relative w-full max-w-4xl mx-auto">
        <!-- Close button -->
        <button onclick="closeTrailer()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
            <i data-lucide="x" class="h-8 w-8"></i>
        </button>
        
        <!-- Video container -->
        <div class="relative w-full" style="padding-bottom: 56.25%;">
            <iframe id="trailer-video" 
                    class="absolute top-0 left-0 w-full h-full rounded-lg" 
                    src="" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
            </iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentRating = 0;

function handleBooking() {
    @if(!session('user'))
        alert('Bạn cần đăng nhập để đặt vé');
        return;
    @endif
    // Redirect to booking page
    window.location.href = '/booking?movie={{ $movie['id'] }}';
}

function handleFavorite(button) {
    @if(!session('user'))
        alert('Bạn cần đăng nhập để sử dụng tính năng yêu thích');
        window.location.href = '/login';
        return;
    @endif

    alert('Tính năng chưa phát triển');
}

function handleShare() {
    @if(!session('user'))
        alert('Bạn cần đăng nhập để chia sẻ phim');
        return;
    @endif
    alert('Tính năng chia sẻ đang phát triển');
}

function handleTrailer() {
    @if(empty($movie['trailer']))
        alert('Trailer không có sẵn');
        return;
    @endif
    
    // Convert YouTube URL to embed URL
    let trailerUrl = '{{ $movie['trailer'] }}';
    let embedUrl = '';
    
    // Handle different YouTube URL formats
    if (trailerUrl.includes('youtube.com/watch?v=')) {
        const videoId = trailerUrl.split('v=')[1].split('&')[0];
        embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
    } else if (trailerUrl.includes('youtu.be/')) {
        const videoId = trailerUrl.split('youtu.be/')[1].split('?')[0];
        embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
    } else if (trailerUrl.includes('youtube.com/embed/')) {
        embedUrl = trailerUrl + '?autoplay=1&rel=0';
    } else {
        // If it's not a YouTube URL, try to open in new tab
        window.open(trailerUrl, '_blank');
        return;
    }
    
    // Set video source and show modal
    document.getElementById('trailer-video').src = embedUrl;
    document.getElementById('trailer-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeTrailer() {
    // Stop video and hide modal
    document.getElementById('trailer-video').src = '';
    document.getElementById('trailer-modal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close modal when clicking outside or pressing ESC
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('trailer-modal');
    
    // Close when clicking outside the video
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeTrailer();
        }
    });
    
    // Close when pressing ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeTrailer();
        }
    });
});

function setRating(rating) {
    console.log('setRating called with:', rating);
    currentRating = rating;
    document.getElementById('rating-input').value = rating;
    
    // Update star display
    const stars = document.querySelectorAll('.star-rating');
    console.log('Found stars:', stars.length);
    if (stars.length > 0) {
        stars.forEach((star, index) => {
            if (index + 1 <= rating) {
                // Set yellow star
                star.innerHTML = '<i data-lucide="star" class="h-full w-full" style="color: #fbbf24; fill: #fbbf24;"></i>';
                star.style.color = '#fbbf24';
                console.log('Star', index + 1, 'set to yellow');
            } else {
                // Set gray star
                star.innerHTML = '<i data-lucide="star" class="h-full w-full" style="color: #d1d5db; fill: none;"></i>';
                star.style.color = '#d1d5db';
                console.log('Star', index + 1, 'set to gray');
            }
        });
        
        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
}

// Handle review form submission
document.getElementById('review-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Đang gửi...';
    submitButton.disabled = true;
    
    try {
        const response = await fetch('/api/movies/{{ $movie['id'] }}/reviews', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                rating: formData.get('rating'),
                comment: formData.get('comment'),
                user_id: {{ session('user')['id'] ?? 'null' }}
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Đánh giá đã được gửi thành công!');
            location.reload();
        } else {
            throw new Error(result.message || 'Gửi đánh giá thất bại');
        }
    } catch (error) {
        alert('Lỗi: ' + error.message);
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
});

// Handle comment form submission
document.getElementById('comment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Đang gửi...';
    submitButton.disabled = true;
    
    try {
        const response = await fetch('/api/movies/{{ $movie['id'] }}/comments', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                content: formData.get('content'),
                user_id: {{ session('user')['id'] ?? 'null' }}
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Bình luận đã được gửi thành công!');
            location.reload();
        } else {
            throw new Error(result.message || 'Gửi bình luận thất bại');
        }
    } catch (error) {
        alert('Lỗi: ' + error.message);
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
});

// Toggle review reply form
function toggleReviewReply(reviewId) {
    const replyForm = document.getElementById('review-reply-' + reviewId);
    if (replyForm) {
        replyForm.classList.toggle('hidden');
    }
}

// Toggle comment reply form
function toggleCommentReply(commentId) {
    const replyForm = document.getElementById('comment-reply-' + commentId);
    if (replyForm) {
        replyForm.classList.toggle('hidden');
    }
}

// Handle review reply form submission
document.addEventListener('submit', async function(e) {
    if (e.target.classList.contains('review-reply-form')) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const reviewId = e.target.dataset.reviewId;
        const submitButton = e.target.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Show loading state
        submitButton.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Đang gửi...';
        submitButton.disabled = true;
        
        try {
            const response = await fetch('/api/reviews/' + reviewId + '/reply', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    content: formData.get('content'),
                    user_id: {{ session('user')['id'] ?? 'null' }}
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Phản hồi đã được gửi thành công!');
                location.reload();
            } else {
                throw new Error(result.message || 'Gửi phản hồi thất bại');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    }
    
    if (e.target.classList.contains('comment-reply-form')) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const commentId = e.target.dataset.commentId;
        const submitButton = e.target.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Show loading state
        submitButton.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Đang gửi...';
        submitButton.disabled = true;
        
        try {
            const response = await fetch('/api/comments/' + commentId + '/reply', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    content: formData.get('content'),
                    user_id: {{ session('user')['id'] ?? 'null' }}
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Phản hồi đã được gửi thành công!');
                location.reload();
            } else {
                throw new Error(result.message || 'Gửi phản hồi thất bại');
            }
        } catch (error) {
            alert('Lỗi: ' + error.message);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    }
});

</script>

<style>
/* Star rating styles - Force override */
.star-rating {
    transition: all 0.2s ease !important;
}

.star-rating.text-yellow-400 {
    color: #fbbf24 !important;
}

.star-rating.text-yellow-400 i {
    fill: #fbbf24 !important;
    color: #fbbf24 !important;
}

.star-rating.text-gray-300 {
    color: #d1d5db !important;
}

.star-rating.text-gray-300 i {
    fill: none !important;
    color: #d1d5db !important;
}

/* Force yellow color for selected stars */

/* Trailer Modal Styles */
#trailer-modal {
    transition: opacity 0.3s ease-in-out;
}

#trailer-modal.hidden {
    opacity: 0;
    pointer-events: none;
}

#trailer-modal:not(.hidden) {
    opacity: 1;
    pointer-events: auto;
}

#trailer-modal .relative {
    transform: scale(0.9);
    transition: transform 0.3s ease-in-out;
}

#trailer-modal:not(.hidden) .relative {
    transform: scale(1);
}

/* Close button hover effect */
#trailer-modal button:hover {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}
.star-rating[data-rating] {
    transition: all 0.2s ease !important;
}

.star-rating:hover {
    color: #fbbf24 !important;
}

.star-rating:hover i {
    fill: #fbbf24 !important;
}

/* Override any conflicting styles */
#rating-stars .star-rating {
    color: #d1d5db !important;
}

#rating-stars .star-rating i {
    fill: none !important;
    color: #d1d5db !important;
}

#rating-stars .star-rating.text-yellow-400 {
    color: #fbbf24 !important;
}

#rating-stars .star-rating.text-yellow-400 i {
    fill: #fbbf24 !important;
    color: #fbbf24 !important;
}
</style>
@endpush
