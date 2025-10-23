@extends('layouts.app')

@section('title', 'Thể loại phim - Phim Việt')
@section('description', 'Khám phá bộ sưu tập phim Việt Nam đa dạng theo từng thể loại')

@section('content')
<div class="min-h-screen">
    <main class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-foreground mb-4">Thể loại phim</h1>
                <p class="text-muted-foreground text-lg">
                    Khám phá bộ sưu tập phim Việt Nam đa dạng theo từng thể loại
                </p>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <form method="GET" action="{{ route('movies.genres') }}" class="relative max-w-md">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground h-4 w-4"></i>
                    <input
                        type="text"
                        name="search"
                        placeholder="Tìm kiếm phim..."
                        value="{{ $searchQuery }}"
                        class="input w-full pl-10"
                    >
                </form>
            </div>

            <!-- Genre Filter -->
            <div class="mb-8">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('movies.genres', array_merge(request()->query(), ['category' => 'Tất cả'])) }}" 
                       class="btn {{ $selectedGenre === 'Tất cả' ? 'btn-primary' : 'btn-outline' }} mb-2">
                        Tất cả
                    </a>
                    @foreach($availableGenres as $genre)
                        <a href="{{ route('movies.genres', array_merge(request()->query(), ['category' => $genre])) }}" 
                           class="btn {{ $selectedGenre === $genre ? 'btn-primary' : 'btn-outline' }} mb-2">
                            {{ $genre }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Movies Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @forelse($filteredMovies as $movie)
                    <div class="group overflow-hidden transition-all duration-300 hover:shadow-lg hover:scale-105 h-full p-0 card">
                        <a href="{{ route('movies.show', $movie['id']) }}">
                            <div class="relative aspect-[2/3] overflow-hidden w-full">
                                <img src="{{ $movie['poster'] }}" 
                                     alt="{{ $movie['title'] }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                <!-- Rating badge -->
                                <div class="absolute top-2 right-2">
                                    <div class="badge bg-black/70 text-white border-0">
                                        <i data-lucide="star" class="h-3 w-3 mr-1 fill-current text-accent"></i>
                                        @if(isset($movie['rating']) && $movie['rating'] !== null)
                                            {{ number_format($movie['rating'], 1) }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>

                                <!-- Featured badge -->
                                @if(isset($movie['featured']) && $movie['featured'])
                                    <div class="absolute top-2 left-2">
                                        <div class="badge bg-accent text-accent-foreground">Nổi bật</div>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div class="p-3 md:p-4 flex-1 flex flex-col">
                            <h3 class="font-semibold text-card-foreground line-clamp-2 mb-2 group-hover:text-primary transition-colors text-sm md:text-base">
                                <a href="{{ route('movies.show', $movie['id']) }}" class="hover:text-primary transition-colors">
                                    {{ $movie['title'] }}
                                </a>
                            </h3>

                            <div class="flex items-center justify-between text-xs md:text-sm text-muted-foreground mb-2">
                                <span>{{ date('Y', strtotime($movie['release_date'])) }}</span>
                                <div class="flex items-center">
                                    <i data-lucide="clock" class="h-3 w-3 mr-1"></i>
                                    {{ $movie['duration'] }} phút
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-1 mt-auto">
                                @if(isset($movie['genre']) && is_array($movie['genre']))
                                    @foreach(array_slice($movie['genre'], 0, 2) as $genre)
                                        <div class="badge text-xs">{{ $genre }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-muted-foreground text-lg">Không tìm thấy phim nào phù hợp với tiêu chí tìm kiếm</p>
                    </div>
                @endforelse
            </div>

            <!-- Results Count -->
            <div class="mt-8 text-center">
                <p class="text-muted-foreground">
                    Hiển thị {{ count($filteredMovies) }} phim
                    @if($selectedGenre !== 'Tất cả')
                        trong thể loại "{{ $selectedGenre }}"
                    @endif
                </p>
            </div>
        </div>
    </main>
</div>
@endsection