@extends('layouts.admin')

@section('title', 'Quản lý phim')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quản lý phim</h1>
            <p class="text-gray-600 mt-1">Quản lý thư viện phim của bạn</p>
        </div>
        <a href="{{ route('admin.movies.new') }}" class="add-movie-btn inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold transition-all duration-200">
            <i data-lucide="plus" class="h-5 w-5"></i>
            Thêm phim mới
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center gap-4">
            <div class="relative flex-1">
                <i data-lucide="search" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="movieSearch" placeholder="Tìm kiếm phim theo tên, thể loại..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
            </div>
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Lọc
            </button>
        </div>
    </div>

    <!-- Movies Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poster</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên phim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thể loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Năm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đánh giá</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($movies) > 0)
                        @foreach($movies as $movie)
                            @if(is_array($movie))
                            <tr class="hover:bg-gray-50">
                                <!-- Poster -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-12 h-16 bg-gray-200 rounded overflow-hidden">
                                        @if(isset($movie['poster']) && $movie['poster'])
                                            <img src="{{ \App\Helpers\ImageHelper::getMoviePoster($movie) }}" 
                                                 alt="{{ $movie['title'] }}" 
                                                 class="w-full h-full object-cover"
                                                 onerror="console.log('Image failed to load:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                 onload="console.log('Image loaded successfully:', this.src);"
                                                 loading="lazy">
                                            <div class="w-full h-full flex items-center justify-center text-gray-400" style="display: none;">
                                                <i data-lucide="image" class="h-6 w-6"></i>
                                            </div>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i data-lucide="image" class="h-6 w-6"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Movie Title -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $movie['title'] ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $movie['original_title'] ?? $movie['title'] ?? 'N/A' }}</div>
                                    </div>
                                </td>

                                <!-- Genre -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            // Handle genres from different sources
                                            $genres = [];
                                            if (isset($movie['genres']) && is_array($movie['genres'])) {
                                                // From API or session (array of objects)
                                                $genres = $movie['genres'];
                                            } elseif (isset($movie['genre']) && is_array($movie['genre'])) {
                                                // From API (array of strings)
                                                $genres = $movie['genre'];
                                            }
                                        @endphp
                                        
                                        @if(count($genres) > 0)
                                            @foreach(array_slice($genres, 0, 2) as $genre)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                                    {{ is_array($genre) ? ($genre['name'] ?? $genre) : $genre }}
                                                </span>
                                            @endforeach
                                            @if(count($genres) > 2)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full">
                                                    +{{ count($genres) - 2 }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                                N/A
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Year -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(isset($movie['release_date']) && $movie['release_date'])
                                        {{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <!-- Rating -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="star" class="h-4 w-4 text-yellow-500 fill-yellow-500"></i>
                                        <span class="text-sm text-gray-900">{{ number_format($movie['rating'] ?? 0, 1) }}</span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($movie['featured'] ?? false)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                            Nổi bật
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                                            Thường
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.movies.edit', $movie['id']) }}" 
                                           class="p-1 text-blue-600 hover:text-blue-800 transition-colors" 
                                           title="Chỉnh sửa">
                                            <i data-lucide="edit" class="h-4 w-4"></i>
                                        </a>
                                        <button class="p-1 text-red-600 hover:text-red-800 transition-colors delete-movie-btn" 
                                                title="Xóa"
                                                data-movie-id="{{ $movie['id'] }}"
                                                data-movie-title="{{ $movie['title_vi'] ?? $movie['title'] }}">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="film" class="h-12 w-12 text-gray-400 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có phim nào</h3>
                                    <p class="text-gray-500 mb-4">Bắt đầu bằng cách thêm phim mới vào hệ thống</p>
                                    <a href="{{ route('admin.movies.new') }}" class="add-movie-btn inline-flex items-center gap-2 px-6 py-3 rounded-lg text-sm font-semibold transition-all duration-200">
                                        <i data-lucide="plus" class="h-5 w-5"></i>
                                        Thêm phim mới
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .add-movie-btn {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: white !important;
        font-weight: 600 !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
        box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.3) !important;
        border: 2px solid #1e40af !important;
    }
    
    .add-movie-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px 0 rgba(37, 99, 235, 0.4) !important;
    }
    
    .add-movie-btn i {
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1)) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const movieSearch = document.getElementById('movieSearch');
        const tableRows = document.querySelectorAll('tbody tr');

        function filterMovies() {
            const searchTerm = movieSearch.value.toLowerCase();
            
            tableRows.forEach(row => {
                if (row.querySelector('td[colspan]')) return; // Skip empty state row
                
                const titleCell = row.querySelector('td:nth-child(2)');
                const genreCell = row.querySelector('td:nth-child(3)');
                
                if (titleCell && genreCell) {
                    const title = titleCell.textContent.toLowerCase();
                    const genre = genreCell.textContent.toLowerCase();
                    
                    const matches = title.includes(searchTerm) || genre.includes(searchTerm);
                    row.style.display = matches ? '' : 'none';
                }
            });
        }

        movieSearch.addEventListener('input', filterMovies);

        // Handle delete movie buttons
        const deleteButtons = document.querySelectorAll('.delete-movie-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const movieId = this.getAttribute('data-movie-id');
                const movieTitle = this.getAttribute('data-movie-title');
                
                if (confirm(`Bạn có chắc chắn muốn xóa phim "${movieTitle}"?`)) {
                    // Create a form to submit DELETE request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/movies/${movieId}`;
                    
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
                    
                    // Submit the form
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
