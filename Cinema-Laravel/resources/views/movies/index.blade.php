@extends('layouts.app')

@section('title', 'Tất cả phim - Phim Việt')
@section('description', 'Khám phá toàn bộ bộ sưu tập phim Việt Nam của chúng tôi')

@section('content')
<div class="min-h-screen bg-background flex flex-col">
    <main class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-foreground mb-4">Tất cả phim</h1>
                <p class="text-muted-foreground text-lg">Khám phá toàn bộ bộ sưu tập phim Việt Nam của chúng tôi</p>
            </div>

            <!-- Search and Filters -->
            <div class="mb-8">
                <form method="GET" action="{{ route('movies.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                    <!-- Search (takes 2 cols on md+) -->
                    <div class="md:col-span-2">
                        <label class="sr-only">Tìm kiếm</label>
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4"></i>
                            <input
                                type="text"
                                name="search"
                                id="search-input"
                                placeholder="Tìm kiếm phim, đạo diễn..."
                                value="{{ request('search') }}"
                                class="pl-10 w-full h-10 px-3 py-2 border border-input bg-background text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 rounded-md"
                                autocomplete="off"
                            />
                            
                            <!-- Search Suggestions Dropdown -->
                            <div id="search-suggestions" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg z-50 hidden max-h-60 overflow-y-auto">
                                <!-- Suggestions will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Filters and actions -->
                    <div class="flex items-center gap-2 justify-start md:justify-end">
                        <div class="hidden sm:flex items-center gap-2 text-sm text-muted-foreground">
                            <i data-lucide="filter" class="h-4 w-4"></i>
                            <span class="font-medium">Lọc:</span>
                        </div>

                        <select name="sort" class="w-40 h-10 px-3 py-2 border border-input bg-background text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 rounded-md">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Đánh giá cao nhất</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Tên A-Z</option>
                        </select>

                        <select name="year" class="w-36 h-10 px-3 py-2 border border-input bg-background text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 rounded-md">
                            <option value="all" {{ request('year') == 'all' ? 'selected' : '' }}>Tất cả</option>
                            <option value="2024" {{ request('year') == '2024' ? 'selected' : '' }}>2024</option>
                            <option value="2023" {{ request('year') == '2023' ? 'selected' : '' }}>2023</option>
                            <option value="2022" {{ request('year') == '2022' ? 'selected' : '' }}>2022</option>
                            <option value="2021" {{ request('year') == '2021' ? 'selected' : '' }}>2021</option>
                            <option value="2020" {{ request('year') == '2020' ? 'selected' : '' }}>2020</option>
                            <option value="2010s" {{ request('year') == '2010s' ? 'selected' : '' }}>2010s</option>
                            <option value="2000s" {{ request('year') == '2000s' ? 'selected' : '' }}>2000s</option>
                            <option value="1990s" {{ request('year') == '1990s' ? 'selected' : '' }}>1990s</option>
                            <option value="before1990" {{ request('year') == 'before1990' ? 'selected' : '' }}>Trước 1990</option>
                        </select>

                        <a href="{{ route('movies.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                            Xóa
                        </a>
                    </div>
                </form>
            </div>

            <!-- Results Info -->
            <div class="mb-6">
                <p class="text-muted-foreground">
                    Hiển thị {{ $movies->count() }} trong tổng số {{ $movies->total() }} phim
                </p>
            </div>

            <!-- Movies Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8">
                @forelse($movies as $movie)
                    <a href="{{ route('movies.show.slug', $movie['slug'] ?? $movie['id']) }}" class="group block">
                        <div class="relative aspect-[2/3] overflow-hidden rounded-lg mb-3">
                            <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($movie['poster']) }}" 
                                 alt="{{ $movie['title'] }}" 
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                 onerror="this.src='/images/placeholder-movie.svg'">
                            
                            <!-- Featured badge -->
                            @if(isset($movie['featured']) && $movie['featured'])
                                <div class="absolute top-2 left-2">
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-orange-500 text-white">Nổi bật</div>
                                </div>
                            @endif

                            <!-- Rating badge -->
                            <div class="absolute top-2 right-2">
                                <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 bg-black/70 text-white border-0">
                                    <i data-lucide="star" class="h-3 w-3 mr-1 fill-current text-yellow-400"></i>
                                    @if(isset($movie['rating']) && $movie['rating'] !== null)
                                        {{ number_format($movie['rating'], 1) }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Movie Info -->
                        <div class="space-y-2">
                            <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors text-sm md:text-base line-clamp-2">
                                {{ $movie['title'] }}
                            </h3>

                            <div class="flex items-center justify-between text-xs md:text-sm text-muted-foreground">
                                <span>{{ date('Y', strtotime($movie['release_date'])) }}</span>
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                                    {{ $movie['duration'] }} phút
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-1">
                                @if(isset($movie['genre']) && is_array($movie['genre']))
                                    @foreach(array_slice($movie['genre'], 0, 2) as $genre)
                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium text-muted-foreground border-gray-300 bg-gray-100">{{ $genre }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <!-- No Results -->
                    <div class="col-span-full text-center py-12">
                        <p class="text-muted-foreground text-lg mb-4">
                            Không tìm thấy phim nào phù hợp với tiêu chí tìm kiếm
                        </p>
                        <a href="{{ route('movies.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                            Xóa bộ lọc
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($movies->hasPages())
                <div class="flex justify-center items-center gap-2">
                    <!-- Previous Button -->
                    @if($movies->onFirstPage())
                        <button disabled class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 opacity-50 cursor-not-allowed">
                            Trước
                        </button>
                    @else
                        <a href="{{ $movies->previousPageUrl() }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                            Trước
                        </a>
                    @endif

                    <!-- Page Numbers -->
                    <div class="flex gap-1">
                        @php
                            $currentPage = $movies->currentPage();
                            $lastPage = $movies->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $currentPage)
                                <span class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 w-10">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $movies->url($page) }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 w-10">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor
                    </div>

                    <!-- Next Button -->
                    @if($movies->hasMorePages())
                        <a href="{{ $movies->nextPageUrl() }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                            Sau
                        </a>
                    @else
                        <button disabled class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 opacity-50 cursor-not-allowed">
                            Sau
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </main>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .suggestion-item:hover {
        background-color: #f5f5f5;
    }
    
    .suggestion-item:last-child {
        border-bottom: none;
    }
    
    .suggestion-highlight {
        font-weight: bold;
        color: #2563eb;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const suggestionsDiv = document.getElementById('search-suggestions');
    let searchTimeout;
    let allMovies = [];

    // Load all movies for autocomplete
    fetch('/api/movies')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                allMovies = Array.isArray(data.data) ? data.data : data.data.data || [];
            }
        })
        .catch(error => console.error('Error loading movies:', error));

    // Search input event listener
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            suggestionsDiv.classList.add('hidden');
            return;
        }

        // Debounce search
        searchTimeout = setTimeout(() => {
            searchMovies(query);
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });

    // Show suggestions when focusing on input
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            searchMovies(this.value.trim());
        }
    });

    function searchMovies(query) {
        if (allMovies.length === 0) return;

        const filteredMovies = allMovies.filter(movie => 
            movie.title.toLowerCase().includes(query.toLowerCase()) ||
            (movie.description && movie.description.toLowerCase().includes(query.toLowerCase()))
        ).slice(0, 5); // Limit to 5 suggestions

        if (filteredMovies.length === 0) {
            suggestionsDiv.classList.add('hidden');
            return;
        }

        // Create suggestion items
        suggestionsDiv.innerHTML = filteredMovies.map(movie => {
            const highlightedTitle = highlightText(movie.title, query);
            return `
                <div class="suggestion-item" data-title="${movie.title}">
                    <div class="font-medium">${highlightedTitle}</div>
                    <div class="text-sm text-gray-500">${movie.release_date ? new Date(movie.release_date).getFullYear() : ''}</div>
                </div>
            `;
        }).join('');

        // Add click event listeners to suggestions
        suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const title = this.getAttribute('data-title');
                searchInput.value = title;
                suggestionsDiv.classList.add('hidden');
                // Trigger form submission
                searchInput.closest('form').submit();
            });
        });

        suggestionsDiv.classList.remove('hidden');
    }

    function highlightText(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<span class="suggestion-highlight">$1</span>');
    }

    // Auto-submit form when filters change
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.querySelector('select[name="sort"]');
        const yearSelect = document.querySelector('select[name="year"]');
        const searchInput = document.getElementById('search-input');
        const form = document.querySelector('form');

        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                form.submit();
            });
        }

        if (yearSelect) {
            yearSelect.addEventListener('change', function() {
                form.submit();
            });
        }

        // Add debounced search
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    form.submit();
                }, 500); // Wait 500ms after user stops typing
            });
        }
    });
});
</script>
@endpush