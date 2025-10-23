@extends('layouts.admin')

@section('title', 'Thêm phim mới')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('admin.movies') }}" class="hover:text-gray-700">Quản lý phim</a>
                <i data-lucide="chevron-right" class="h-4 w-4"></i>
                <span>Thêm phim mới</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Thêm phim mới</h1>
            <p class="text-gray-600 mt-1">Điền thông tin cơ bản và media để hiển thị phim trong catalog.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Có lỗi xảy ra:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.movies.create') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <!-- Main column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Thông tin cơ bản</h2>
                </div>
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tên phim (Tiếng Anh) *</label>
                            <input type="text" id="title" name="title" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                        </div>
                        <div>
                            <label for="title_vi" class="block text-sm font-medium text-gray-700 mb-1">Tên phim (Tiếng Việt) *</label>
                            <input type="text" id="title_vi" name="title_vi" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Mô tả (Tiếng Anh) *</label>
                        <textarea id="description" name="description" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required></textarea>
                    </div>
                    <div>
                        <label for="description_vi" class="block text-sm font-medium text-gray-700 mb-1">Mô tả (Tiếng Việt) *</label>
                        <textarea id="description_vi" name="description_vi" rows="4" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required></textarea>
                    </div>
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                        <input type="text" id="slug" name="slug" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                        <p class="text-xs text-gray-500 mt-1">Slug được tạo tự động từ tên tiếng Việt</p>
                    </div>
                </div>
            </div>

            <!-- Movie Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Chi tiết phim</h2>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="release_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày phát hành *</label>
                        <input type="date" id="release_date" name="release_date" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                    </div>
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Thời lượng (phút) *</label>
                        <input type="number" id="duration" name="duration" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                    </div>
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Đánh giá</label>
                        <input type="number" id="rating" name="rating" step="0.1" min="-1" max="10" value="-1" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Quốc gia *</label>
                        <input type="text" id="country" name="country" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                    </div>
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Ngôn ngữ *</label>
                        <input type="text" id="language" name="language" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                    </div>
                    <div>
                        <label for="director" class="block text-sm font-medium text-gray-700 mb-1">Đạo diễn *</label>
                        <input type="text" id="director" name="director" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" required>
                    </div>
                </div>
            </div>

            <!-- Genres -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Thể loại *</h2>
                </div>
                <div class="p-4">
                    <div class="flex flex-wrap gap-2" id="genre-container">
                        @foreach($genres as $genre)
                        <button type="button" data-genre="{{ $genre['name'] }}" class="genre-btn px-3 py-1.5 text-sm rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                            {{ $genre['name'] }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="genre" id="genre-input">
                </div>
            </div>

            <!-- Cast -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Diễn viên *</h2>
                </div>
                <div class="p-4">
                    <!-- Actor suggestions -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Gợi ý diễn viên:</p>
                        <div class="flex flex-wrap gap-2" id="actor-suggestions">
                            @foreach($actors as $actor)
                            <button type="button" data-actor="{{ $actor['name'] }}" class="actor-suggestion-btn px-3 py-1.5 text-sm rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                                {{ $actor['name'] }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Selected actors -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Diễn viên đã chọn:</p>
                        <div id="cast-container" class="flex flex-wrap gap-2"></div>
                    </div>
                    
                    <!-- Manual input -->
                    <div>
                        <input type="text" id="cast-input-field" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500" placeholder="Nhập tên diễn viên và nhấn Enter">
                        <input type="hidden" name="cast" id="cast-hidden-input">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Media -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Hình ảnh & Video</h2>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label for="poster" class="block text-sm font-medium text-gray-700 mb-1">Poster *</label>
                        <input type="file" id="poster" name="poster" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" required onchange="previewImage(event, 'poster-preview')">
                        <img id="poster-preview" src="#" alt="Poster Preview" class="mt-2 rounded-lg" style="display:none; max-width: 100%; height: auto;"/>
                        <div id="poster-upload-status" class="mt-2 text-sm hidden"></div>
                    </div>
                    <div>
                        <label for="backdrop" class="block text-sm font-medium text-gray-700 mb-1">Backdrop *</label>
                        <input type="file" id="backdrop" name="backdrop" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" required onchange="previewImage(event, 'backdrop-preview')">
                        <img id="backdrop-preview" src="#" alt="Backdrop Preview" class="mt-2 rounded-lg" style="display:none; max-width: 100%; height: auto;"/>
                        <div id="backdrop-upload-status" class="mt-2 text-sm hidden"></div>
                    </div>
                    <div>
                        <label for="trailer" class="block text-sm font-medium text-gray-700 mb-1">URL Trailer</label>
                        <input type="url" id="trailer" name="trailer" class="w-full rounded-lg border-gray-300 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Cài đặt</h2>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <label for="featured" class="text-sm font-medium text-gray-700">Phim nổi bật</label>
                        <div class="toggle-switch">
                            <input id="featured" name="featured" type="checkbox" value="1">
                            <span class="toggle-slider"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3">
                <button type="submit" class="add-movie-submit-btn w-full py-3 rounded-lg font-bold text-lg shadow-lg transition-all duration-200 transform hover:scale-105 bg-blue-600 hover:bg-blue-700 text-white border-2 border-blue-500">Thêm phim</button>
                <a href="{{ route('admin.movies') }}" class="cancel-btn w-full text-center py-3 rounded-lg font-semibold text-lg transition-all duration-200 bg-red-600 hover:bg-red-700 text-white border-2 border-red-500">Hủy</a>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .add-movie-submit-btn {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: white !important;
        font-weight: 700 !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
        box-shadow: 0 6px 20px 0 rgba(37, 99, 235, 0.4) !important;
        border: 3px solid #1e40af !important;
        letter-spacing: 0.5px !important;
    }
    
    .add-movie-submit-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%) !important;
        transform: translateY(-3px) scale(1.02) !important;
        box-shadow: 0 8px 25px 0 rgba(37, 99, 235, 0.5) !important;
        border-color: #1e3a8a !important;
    }
    
    .add-movie-submit-btn:active {
        transform: translateY(-1px) scale(1.01) !important;
        box-shadow: 0 4px 15px 0 rgba(37, 99, 235, 0.4) !important;
    }
    
    .add-movie-submit-btn:focus {
        outline: none !important;
        ring: 4px solid rgba(37, 99, 235, 0.3) !important;
    }
    
    .cancel-btn {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        color: white !important;
        font-weight: 600 !important;
        border: 2px solid #991b1b !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
        box-shadow: 0 4px 12px 0 rgba(220, 38, 38, 0.3) !important;
    }
    
    .cancel-btn:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 16px 0 rgba(220, 38, 38, 0.4) !important;
        border-color: #7f1d1d !important;
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from title_vi
        const titleViInput = document.getElementById('title_vi');
        const slugInput = document.getElementById('slug');
        
        titleViInput.addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            
            // Add timestamp to make slug unique
            const timestamp = Date.now();
            slugInput.value = slug + '-' + timestamp;
        });

        // Genre selection
        const genreContainer = document.getElementById('genre-container');
        const genreInput = document.getElementById('genre-input');
        let selectedGenres = [];

        genreContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('genre-btn')) {
                const genre = e.target.dataset.genre;
                if (selectedGenres.includes(genre)) {
                    selectedGenres = selectedGenres.filter(g => g !== genre);
                    e.target.classList.remove('bg-teal-600', 'text-white');
                    e.target.classList.add('bg-white', 'text-gray-700');
                } else {
                    selectedGenres.push(genre);
                    e.target.classList.add('bg-teal-600', 'text-white');
                    e.target.classList.remove('bg-white', 'text-gray-700');
                }
                // Send as JSON array instead of comma-separated string
                genreInput.value = JSON.stringify(selectedGenres);
            }
        });

        // Cast input
        const castContainer = document.getElementById('cast-container');
        const castInputField = document.getElementById('cast-input-field');
        const castHiddenInput = document.getElementById('cast-hidden-input');
        const actorSuggestions = document.getElementById('actor-suggestions');
        let castMembers = [];

        // Actor suggestions click handler
        actorSuggestions.addEventListener('click', function(e) {
            if (e.target.classList.contains('actor-suggestion-btn')) {
                const actor = e.target.dataset.actor;
                if (!castMembers.includes(actor)) {
                    castMembers.push(actor);
                    addCastMemberTag(actor);
                    updateCastHiddenInput();
                    // Mark suggestion as selected
                    e.target.classList.add('bg-teal-600', 'text-white');
                    e.target.classList.remove('bg-white', 'text-gray-700');
                }
            }
        });

        castInputField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && castInputField.value.trim() !== '') {
                e.preventDefault();
                const member = castInputField.value.trim();
                if (!castMembers.includes(member)) {
                    castMembers.push(member);
                    addCastMemberTag(member);
                    updateCastHiddenInput();
                }
                castInputField.value = '';
            }
        });

        function addCastMemberTag(member) {
            const tag = document.createElement('div');
            tag.className = 'flex items-center gap-1 bg-gray-100 text-gray-700 text-sm rounded-full px-2 py-1';
            tag.innerHTML = `
                <span>${member}</span>
                <button type="button" data-member="${member}" class="remove-cast-btn text-gray-500 hover:text-gray-700">&times;</button>
            `;
            castContainer.appendChild(tag);
        }

        castContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-cast-btn')) {
                const member = e.target.dataset.member;
                castMembers = castMembers.filter(m => m !== member);
                e.target.parentElement.remove();
                updateCastHiddenInput();
                
                // Reset suggestion button state
                const suggestionBtn = document.querySelector(`[data-actor="${member}"]`);
                if (suggestionBtn) {
                    suggestionBtn.classList.remove('bg-teal-600', 'text-white');
                    suggestionBtn.classList.add('bg-white', 'text-gray-700');
                }
            }
        });

        function updateCastHiddenInput() {
            // Send as JSON array instead of comma-separated string
            castHiddenInput.value = JSON.stringify(castMembers);
        }
    });

    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
        
        // Upload to Google Drive
        uploadToGoogleDrive(event.target, previewId);
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
                if (preview) {
                    preview.src = data.data.url;
                    preview.style.display = 'block';
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
