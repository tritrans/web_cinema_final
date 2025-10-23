@extends('layouts.admin')

@section('title', 'Chỉnh sửa phim')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.movies') }}" 
               class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Quay lại
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Chỉnh sửa phim</h1>
                <p class="text-gray-600 mt-1">Cập nhật thông tin phim "{{ $movie['title'] ?? 'N/A' }}"</p>
            </div>
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

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="flex items-start">
                <i data-lucide="alert-circle" class="h-5 w-5 mr-2 mt-0.5"></i>
                <div>
                    <h4 class="font-medium">Có lỗi xảy ra:</h4>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('admin.movies.update', $movie['id']) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin cơ bản</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tên phim (Tiếng Việt) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title_vi" value="{{ $movie['title_vi'] ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tên phim (Tiếng Anh) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" value="{{ $movie['title'] ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Slug (URL)
                            </label>
                            <input type="text" name="slug" value="{{ $movie['slug'] ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            <p class="text-sm text-gray-500 mt-1">URL của phim là: movies/{{ $movie['slug'] ?? '' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Mô tả (Tiếng Việt) <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description_vi" rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">{{ $movie['description_vi'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Mô tả (Tiếng Anh) <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description" rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">{{ $movie['description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images & Video -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Hình ảnh & Video</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Poster *
                            </label>
                            <div class="space-y-2">
                                <input type="file" name="poster" accept="image/*" 
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                                       id="poster-input" onchange="previewImage(event, 'poster-preview')">
                                <div id="poster-filename" class="text-sm text-gray-600 font-medium" style="display: none;"></div>
                                <div id="poster-upload-status" class="mt-2 text-sm hidden"></div>
                                @if(isset($movie['poster']) && $movie['poster'])
                                    <div class="text-sm text-gray-500">
                                        <span class="font-medium">Ảnh hiện tại:</span> {{ basename($movie['poster']) }}
                                    </div>
                                @endif
                                <img id="poster-preview" src="#" alt="Poster Preview" class="mt-2 rounded-lg max-w-xs" style="display:none;"/>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Backdrop
                            </label>
                            <div class="space-y-2">
                                <input type="file" name="backdrop" accept="image/*" 
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                                       id="backdrop-input" onchange="previewImage(event, 'backdrop-preview')">
                                <div id="backdrop-filename" class="text-sm text-gray-600 font-medium" style="display: none;"></div>
                                <div id="backdrop-upload-status" class="mt-2 text-sm hidden"></div>
                                @if(isset($movie['backdrop']) && $movie['backdrop'])
                                    <div class="text-sm text-gray-500">
                                        <span class="font-medium">Ảnh hiện tại:</span> {{ basename($movie['backdrop']) }}
                                    </div>
                                @endif
                                <img id="backdrop-preview" src="#" alt="Backdrop Preview" class="mt-2 rounded-lg max-w-xs" style="display:none;"/>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                URL Trailer
                            </label>
                            <input type="url" name="trailer" value="{{ $movie['trailer'] ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Genres & Cast -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thể loại & Diễn viên</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Thể loại <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                <div class="flex flex-wrap gap-2" id="selected-genres">
                                    @if(isset($movie['genres']) && is_array($movie['genres']))
                                        @foreach($movie['genres'] as $genre)
                                            <span class="px-3 py-1 bg-teal-100 text-teal-800 border border-teal-300 rounded-full text-sm flex items-center gap-2">
                                                {{ $genre['name'] ?? $genre }}
                                                <button type="button" onclick="removeGenre(this)" class="text-teal-600 hover:text-teal-800">
                                                    <i data-lucide="x" class="h-3 w-3"></i>
                                                </button>
                                                <input type="hidden" name="genres[]" value="{{ $genre['name'] ?? $genre }}">
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="new-genre" placeholder="Nhập thể loại mới" 
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                                    <button type="button" onclick="addGenre()" 
                                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                                        Thêm
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @if(isset($genres) && is_array($genres))
                                        @foreach($genres as $genre)
                                            <button type="button" onclick="addSuggestedGenre('{{ $genre['name'] }}')" 
                                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                                {{ $genre['name'] }}
                                            </button>
                                        @endforeach
                                    @else
                                        @php
                                            $suggestedGenres = ['Comedy', 'Horror', 'Romance', 'Sci-Fi', 'Adventure', 'Animation', 'Documentary', 'Cách mạng'];
                                        @endphp
                                        @foreach($suggestedGenres as $suggestedGenre)
                                            <button type="button" onclick="addSuggestedGenre('{{ $suggestedGenre }}')" 
                                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                                {{ $suggestedGenre }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Diễn viên <span class="text-red-500">*</span>
                            </label>
                            <div class="space-y-3">
                                <div class="flex flex-wrap gap-2" id="selected-casts">
                                    @if(isset($movie['movie_casts']) && is_array($movie['movie_casts']))
                                        @foreach($movie['movie_casts'] as $cast)
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 border border-blue-300 rounded-full text-sm flex items-center gap-2">
                                                {{ $cast['name'] ?? $cast }}
                                                <button type="button" onclick="removeCast(this)" class="text-blue-600 hover:text-blue-800">
                                                    <i data-lucide="x" class="h-3 w-3"></i>
                                                </button>
                                                <input type="hidden" name="casts[]" value="{{ $cast['name'] ?? $cast }}">
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="new-cast" placeholder="Nhập tên diễn viên" 
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                                    <button type="button" onclick="addCast()" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Thêm
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @if(isset($actors) && is_array($actors))
                                        @foreach($actors as $actor)
                                            <button type="button" onclick="addSuggestedCast('{{ $actor['name'] }}')" 
                                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                                {{ $actor['name'] }}
                                            </button>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Movie Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết phim</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Đạo diễn <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="director" id="director-input" value="{{ $movie['director'] ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if(isset($directors) && is_array($directors))
                                    @foreach($directors as $director)
                                        <button type="button" onclick="setDirector('{{ $director['name'] }}')" 
                                                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                            {{ $director['name'] }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Quốc gia <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="country" value="{{ $movie['country'] ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Ngôn ngữ <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="language" value="{{ $movie['language'] ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ngày phát hành <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="release_date" value="{{ isset($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->format('Y-m-d') : '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Thời lượng (phút) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="duration" value="{{ $movie['duration'] ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Đánh giá <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="rating" value="{{ $movie['rating'] ?? '' }}" step="0.1" min="0" max="10" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
                            <p class="text-sm text-gray-500 mt-1">-1 = N/A (chưa có đánh giá), hoặc nhập giá trị từ 0-10</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">
                                Phim nổi bật
                            </label>
                            <div class="toggle-switch">
                                <input type="checkbox" name="featured" value="1" {{ ($movie['featured'] ?? false) ? 'checked' : '' }} 
                                       id="featured-toggle">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác</h3>
                    <div class="space-y-3">
                        <button type="submit" 
                                class="update-movie-btn w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-bold text-lg shadow-lg transition-all duration-200 transform hover:scale-105 bg-blue-600 hover:bg-blue-700 text-white border-2 border-blue-500">
                            <i data-lucide="save" class="h-5 w-5"></i>
                            Cập nhật phim
                        </button>
                        <a href="{{ route('admin.movies') }}" 
                           class="cancel-update-btn w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold text-lg transition-all duration-200 bg-red-600 hover:bg-red-700 text-white border-2 border-red-500">
                            <i data-lucide="x" class="h-5 w-5"></i>
                            Hủy
                        </a>
                    </div>
                </div>

                <!-- Poster Preview -->
                @if(isset($movie['poster']) && $movie['poster'])
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Xem trước poster</h3>
                    <div class="aspect-[2/3] bg-gray-100 rounded-lg overflow-hidden">
                        <img src="{{ \App\Helpers\ImageHelper::getMoviePoster($movie) }}" alt="{{ $movie['title'] }}" 
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-full h-full flex items-center justify-center text-gray-400" style="display: none;">
                            <i data-lucide="image" class="h-12 w-12"></i>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        // Clean up cast list on page load
        setTimeout(function() {
            const directorInput = document.getElementById('director-input');
            const currentDirector = directorInput ? directorInput.value.trim() : '';
            
            if (currentDirector) {
                const castInputs = document.querySelectorAll('input[name="casts[]"]');
                castInputs.forEach(input => {
                    if (input.value.trim() === currentDirector) {
                        console.warn('Removing director from cast list on page load:', currentDirector);
                        input.parentElement.remove();
                    }
                });
            }
        }, 100);
    });

    // Genre management
    function addGenre() {
        const input = document.getElementById('new-genre');
        const genre = input.value.trim();
        if (genre) {
            addGenreTag(genre);
            input.value = '';
        }
    }

    function addSuggestedGenre(genre) {
        addGenreTag(genre);
    }

    function addGenreTag(genre) {
        const container = document.getElementById('selected-genres');
        const tag = document.createElement('span');
        tag.className = 'px-3 py-1 bg-teal-100 text-teal-800 border border-teal-300 rounded-full text-sm flex items-center gap-2';
        tag.innerHTML = `
            ${genre}
            <button type="button" onclick="removeGenre(this)" class="text-teal-600 hover:text-teal-800">
                <i data-lucide="x" class="h-3 w-3"></i>
            </button>
            <input type="hidden" name="genres[]" value="${genre}">
        `;
        container.appendChild(tag);
        lucide.createIcons();
    }

    function removeGenre(button) {
        button.parentElement.remove();
    }

    // Cast management
    function addCast() {
        const input = document.getElementById('new-cast');
        const cast = input.value.trim();
        if (cast) {
            addCastTag(cast);
            input.value = '';
        }
    }

    function addSuggestedCast(cast) {
        addCastTag(cast);
    }

    // Director management
    function setDirector(director) {
        document.getElementById('director-input').value = director;
    }

    function addCastTag(cast) {
        // Debug: Check if cast is actually a director
        const directorInput = document.getElementById('director-input');
        const currentDirector = directorInput ? directorInput.value : '';
        
        if (cast === currentDirector) {
            console.warn('Warning: Trying to add director as cast member:', cast);
            alert('Không thể thêm đạo diễn làm diễn viên!');
            return;
        }
        
        const container = document.getElementById('selected-casts');
        const tag = document.createElement('span');
        tag.className = 'px-3 py-1 bg-blue-100 text-blue-800 border border-blue-300 rounded-full text-sm flex items-center gap-2';
        tag.innerHTML = `
            ${cast}
            <button type="button" onclick="removeCast(this)" class="text-blue-600 hover:text-blue-800">
                <i data-lucide="x" class="h-3 w-3"></i>
            </button>
            <input type="hidden" name="casts[]" value="${cast}">
        `;
        container.appendChild(tag);
        lucide.createIcons();
    }

    function removeCast(button) {
        button.parentElement.remove();
    }

    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const directorInput = document.getElementById('director-input');
        const currentDirector = directorInput ? directorInput.value.trim() : '';
        
        // Remove any cast members that match the director
        const castInputs = document.querySelectorAll('input[name="casts[]"]');
        castInputs.forEach(input => {
            if (input.value.trim() === currentDirector) {
                console.warn('Removing director from cast list:', currentDirector);
                input.parentElement.remove();
            }
        });
        
        // Check if we still have cast members
        const remainingCastInputs = document.querySelectorAll('input[name="casts[]"]');
        if (remainingCastInputs.length === 0) {
            e.preventDefault();
            alert('Phải có ít nhất 1 diễn viên!');
            return false;
        }
    });

    // File input handling with preview and filename display
    document.getElementById('poster-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            console.log('Poster file selected:', file.name);
            // Show filename
            const filenameDiv = document.getElementById('poster-filename');
            filenameDiv.textContent = 'File đã chọn: ' + file.name;
            filenameDiv.style.display = 'block';
        }
    });

    document.getElementById('backdrop-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            console.log('Backdrop file selected:', file.name);
            // Show filename
            const filenameDiv = document.getElementById('backdrop-filename');
            filenameDiv.textContent = 'File đã chọn: ' + file.name;
            filenameDiv.style.display = 'block';
        }
    });

    // Image preview function
    function previewImage(event, previewId) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            
            // Upload to Google Drive
            uploadToGoogleDrive(event.target, previewId);
        }
    }
    
    function uploadToGoogleDrive(input, previewId) {
        const file = input.files[0];
        if (!file) return;
        
        const statusDiv = document.getElementById(previewId.replace('-preview', '-upload-status'));
        const type = input.name; // 'poster' or 'backdrop'
        
        // Show uploading status
        statusDiv.innerHTML = '<span class="text-blue-600">🔄 Đang upload lên Google Drive...</span>';
        statusDiv.classList.remove('hidden');
        
        // Create FormData
        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);
        
        // Upload to API
        fetch('/api/upload/file', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': 'Bearer ' + (localStorage.getItem('jwt_token') || '')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store the Google Drive URL in a hidden input
                const hiddenInput = document.getElementById(type + '_url');
                if (!hiddenInput) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.id = type + '_url';
                    input.name = type + '_url';
                    input.value = data.data.url;
                    document.querySelector('form').appendChild(input);
                } else {
                    hiddenInput.value = data.data.url;
                }
                
                // Update the preview image with the new Google Drive URL
                const preview = document.getElementById(previewId);
                console.log('Updating preview:', previewId, 'with URL:', data.data.url);
                if (preview) {
                    preview.src = data.data.url;
                    preview.style.display = 'block';
                    console.log('Preview updated successfully');
                } else {
                    console.error('Preview element not found:', previewId);
                }
                
                // Update the "Ảnh hiện tại" text
                const currentImageDiv = document.querySelector('#poster-input').parentElement.querySelector('.text-sm.text-gray-500');
                console.log('Current image div found:', !!currentImageDiv);
                if (currentImageDiv) {
                    currentImageDiv.innerHTML = '<span class="font-medium">Ảnh hiện tại:</span> ' + data.data.url;
                    console.log('Current image text updated');
                } else {
                    console.error('Current image div not found');
                }
                
                // Also update the large preview in the right sidebar
                const largePreview = document.querySelector('.aspect-\\[2\\/3\\] img');
                console.log('Large preview found:', !!largePreview);
                if (largePreview) {
                    largePreview.src = data.data.url;
                    console.log('Large preview updated');
                } else {
                    console.error('Large preview not found');
                }
                
                statusDiv.innerHTML = '<span class="text-green-600">✅ Upload thành công!</span>';
                console.log('Upload successful:', data.data.url);
            } else {
                statusDiv.innerHTML = '<span class="text-red-600">❌ Upload thất bại: ' + (data.message || 'Lỗi không xác định') + '</span>';
                console.error('Upload failed:', data);
            }
        })
        .catch(error => {
            statusDiv.innerHTML = '<span class="text-red-600">❌ Lỗi kết nối: ' + error.message + '</span>';
            console.error('Upload error:', error);
        });
    }
</script>
@endpush
@endsection
