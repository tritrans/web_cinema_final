@extends('layouts.app')

@section('title', 'Phim yêu thích - Phim Việt')
@section('description', 'Danh sách phim yêu thích của bạn')

@section('content')
<div class="min-h-screen bg-background">
    <main class="container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-foreground">Phim yêu thích</h1>
                    <p class="text-muted-foreground mt-2">
                        Danh sách phim bạn đã thêm vào yêu thích
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <i data-lucide="heart" class="h-4 w-4"></i>
                    <span>{{ count($favorites) }} phim</span>
                </div>
            </div>
        </div>

        <!-- Favorites List -->
        @php
            $validFavorites = array_filter($favorites, function($favorite) {
                return is_array($favorite) && isset($favorite['movie']);
            });
        @endphp
        
        @if(count($validFavorites) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($validFavorites as $favorite)
                    @php
                        $movie = $favorite['movie'] ?? $favorite;
                    @endphp
                    <div class="bg-card border rounded-lg shadow-sm hover:shadow-lg transition-shadow group">
                        <!-- Movie Poster -->
                        <div class="relative">
                            <div class="aspect-[2/3] overflow-hidden rounded-t-lg">
                                <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($movie['poster']) }}" 
                                     alt="{{ $movie['title'] }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     onerror="this.src='/images/placeholder-movie.svg'">
                            </div>
                            
                            <!-- Remove from favorites button -->
                            <button onclick="removeFromFavorites({{ $movie['id'] }}, this)" 
                                    class="absolute top-2 right-2 p-2 bg-red-500/80 hover:bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                                    title="Bỏ yêu thích">
                                <i data-lucide="heart" class="h-4 w-4 fill-current"></i>
                            </button>
                            
                            <!-- Rating -->
                            @if(isset($movie['rating']) && $movie['rating'])
                                <div class="absolute bottom-2 left-2 flex items-center gap-1 bg-black/70 text-white px-2 py-1 rounded-full text-sm">
                                    <i data-lucide="star" class="h-3 w-3 fill-current text-yellow-400"></i>
                                    <span>{{ $movie['rating'] }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Movie Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-foreground mb-2 line-clamp-2">
                                {{ $movie['title'] }}
                            </h3>
                            
                            <div class="space-y-2 text-sm text-muted-foreground">
                                @if(isset($movie['release_date']))
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="calendar" class="h-4 w-4"></i>
                                        <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}</span>
                                    </div>
                                @endif
                                
                                @if(isset($movie['duration']))
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="h-4 w-4"></i>
                                        <span>{{ $movie['duration'] }} phút</span>
                                    </div>
                                @endif
                                
                                @if(isset($movie['genre']) && is_array($movie['genre']) && count($movie['genre']) > 0)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="tag" class="h-4 w-4"></i>
                                        <span>{{ implode(', ', array_slice($movie['genre'], 0, 2)) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('movies.show.slug', $movie['slug'] ?? $movie['id']) }}" 
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 font-medium text-sm">
                                    <i data-lucide="play" class="h-4 w-4"></i>
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-card border rounded-lg shadow-sm">
                <div class="py-12 text-center">
                    <i data-lucide="heart" class="h-12 w-12 mx-auto mb-4 text-muted-foreground opacity-50"></i>
                    <h3 class="text-lg font-semibold mb-2">Chưa có phim yêu thích</h3>
                    <p class="text-muted-foreground mb-4">
                        @if(count($favorites) > 0)
                            Có dữ liệu nhưng không hợp lệ. Vui lòng thử lại sau.
                        @else
                            Bạn chưa thêm phim nào vào danh sách yêu thích. Hãy khám phá các bộ phim và thêm vào yêu thích!
                        @endif
                    </p>
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                        Khám phá phim
                    </a>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection

@push('scripts')
<script>
function removeFromFavorites(movieId, button) {
    const originalContent = button.innerHTML;
    button.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i>';
    button.disabled = true;
    
    fetch(`/api/favorites/${movieId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the movie card from the page
            button.closest('.bg-card').remove();
            
            // Update the count
            const countElement = document.querySelector('.flex.items-center.gap-2.text-sm.text-muted-foreground span');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent);
                countElement.textContent = Math.max(0, currentCount - 1) + ' phim';
            }
            
            // Show success message
            alert('Đã bỏ yêu thích phim này!');
        } else {
            throw new Error(data.message || 'Bỏ yêu thích thất bại');
        }
    })
    .catch(error => {
        alert('Lỗi: ' + error.message);
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}
</script>
@endpush
