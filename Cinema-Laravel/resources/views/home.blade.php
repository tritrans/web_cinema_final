@extends('layouts.app')

@section('title', 'Trang chủ - Phim Việt')
@section('description', 'Xem phim Việt Nam chất lượng cao - Watch high-quality Vietnamese movies')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Hero Section - Clean white background -->
    @if(isset($heroMovie) && $heroMovie)
        <section class="relative h-[70vh] min-h-[500px] overflow-hidden">
            <!-- Background Image with dark overlay -->
            <div class="absolute inset-0">
                <img src="{{ \App\Helpers\ImageHelper::getMovieBackdrop($heroMovie) }}" 
                     alt="{{ $heroMovie['title'] }}" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
            </div>

            <!-- Content -->
            <div class="relative container h-full flex items-center px-4 md:px-8 lg:px-16">
                <div class="max-w-2xl space-y-6">
                    <!-- Featured Badge -->
                    <div class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold bg-yellow-500 text-black">Phim nổi bật</div>

                    <!-- Title -->
                    <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight">{{ $heroMovie['title'] }}</h1>

                    <!-- Movie Info -->
                    <div class="flex items-center space-x-4 text-white/90">
                        <div class="flex items-center">
                            <i data-lucide="star" class="h-4 w-4 mr-1 fill-current text-yellow-400"></i>
                            <span class="font-medium">
                                @if(isset($heroMovie['rating']) && $heroMovie['rating'] !== null)
                                    {{ number_format($heroMovie['rating'], 1) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <span>•</span>
                        <span>{{ date('Y', strtotime($heroMovie['release_date'])) }}</span>
                        <span>•</span>
                        <span>{{ $heroMovie['duration'] }} phút</span>
                        <span>•</span>
                        <div class="flex space-x-2">
                            @if(isset($heroMovie['genre']) && is_array($heroMovie['genre']))
                                @foreach(array_slice($heroMovie['genre'], 0, 2) as $genre)
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium text-white bg-white/20 border-white/30">{{ $genre }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-lg text-white/90 leading-relaxed max-w-xl">{{ $heroMovie['description'] }}</p>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('movies.show.slug', $heroMovie['slug'] ?? $heroMovie['id']) }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all bg-green-600 text-white shadow-sm hover:bg-green-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 h-10 px-6">
                            <i data-lucide="play" class="h-5 w-5"></i>
                            Xem ngay
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Featured Movies Section -->
    <section class="py-16 px-4 bg-white">
        <div class="container max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Phim nổi bật</h2>
                <a href="{{ route('movies.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors font-medium px-2 py-1 flex items-center">
                    Xem tất cả
                    <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                </a>
            </div>

            @if(isset($featuredMovies) && count($featuredMovies) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-6 md:gap-8">
                    @foreach($featuredMovies as $movie)
                        <a href="{{ route('movies.show.slug', $movie['slug'] ?? $movie['id']) }}" class="group block bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="{{ \App\Helpers\ImageHelper::getMoviePoster($movie) }}" 
                                     alt="{{ $movie['title'] }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='/images/placeholder-movie.svg'; this.style.opacity='0.7';">
                                
                                <!-- Featured badge -->
                                <div class="absolute top-3 left-3">
                                    <div class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-orange-500 text-white shadow-sm">Nổi bật</div>
                                </div>

                                <!-- Rating badge -->
                                <div class="absolute top-3 right-3">
                                    <div class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-black/80 text-white shadow-sm">
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
                            <div class="p-4 space-y-3">
                                <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors text-base line-clamp-2">
                                    {{ $movie['title'] }}
                                </h3>

                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span class="font-medium">{{ date('Y', strtotime($movie['release_date'])) }}</span>
                                    <div class="flex items-center">
                                        <i data-lucide="clock" class="h-4 w-4 mr-1"></i>
                                        <span class="font-medium">{{ $movie['duration'] }} phút</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if(isset($movie['genre']) && is_array($movie['genre']))
                                        @foreach(array_slice($movie['genre'], 0, 2) as $genre)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200">{{ $genre }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Loading skeleton -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-6 md:gap-8">
                    @for($i = 0; $i < 8; $i++)
                        <div class="aspect-[2/3] bg-muted animate-pulse rounded-lg"></div>
                    @endfor
                </div>
            @endif
        </div>
    </section>

    <!-- Recent Movies Section -->
    <section class="py-16 px-4 bg-gray-50">
        <div class="container max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Phim mới nhất</h2>
                <a href="{{ route('movies.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors font-medium px-2 py-1 flex items-center">
                    Xem tất cả
                    <i data-lucide="chevron-right" class="h-4 w-4 ml-1"></i>
                </a>
            </div>
            
            
            
            
            
            <!-- Debug: recentMovies isset: {{ isset($recentMovies) ? 'true' : 'false' }}, count: {{ isset($recentMovies) ? count($recentMovies) : 'N/A' }} -->
            @if(isset($recentMovies) && count($recentMovies) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-6 md:gap-8">
                    @foreach($recentMovies as $movie)
                        <a href="{{ route('movies.show.slug', $movie['slug'] ?? $movie['id']) }}" class="group block bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden">
                            <div class="relative aspect-[2/3] overflow-hidden">
                                <img src="{{ \App\Helpers\ImageHelper::getMoviePoster($movie) }}" 
                                     alt="{{ $movie['title'] }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='/images/placeholder-movie.svg'; this.style.opacity='0.7';">
                                
                                <!-- Featured badge -->
                                <div class="absolute top-3 left-3">
                                    <div class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-orange-500 text-white shadow-sm">Nổi bật</div>
                                </div>

                                <!-- Rating badge -->
                                <div class="absolute top-3 right-3">
                                    <div class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-black/80 text-white shadow-sm">
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
                            <div class="p-4 space-y-3">
                                <h3 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors text-base line-clamp-2">
                                    {{ $movie['title'] }}
                                </h3>

                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span class="font-medium">{{ date('Y', strtotime($movie['release_date'])) }}</span>
                                    <div class="flex items-center">
                                        <i data-lucide="clock" class="h-4 w-4 mr-1"></i>
                                        <span class="font-medium">{{ $movie['duration'] }} phút</span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if(isset($movie['genre']) && is_array($movie['genre']))
                                        @foreach(array_slice($movie['genre'], 0, 2) as $genre)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 border border-gray-200">{{ $genre }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Loading skeleton -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-6 md:gap-8">
                    @for($i = 0; $i < 8; $i++)
                        <div class="aspect-[2/3] bg-muted animate-pulse rounded-lg"></div>
                    @endfor
                </div>
            @endif
        </div>
    </section>

</div>
@endsection

@push('styles')
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
</style>
@endpush