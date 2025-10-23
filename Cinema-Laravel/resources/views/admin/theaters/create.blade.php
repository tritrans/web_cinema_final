@extends('layouts.admin')

@section('title', 'Thêm rạp chiếu mới')

@php
    $breadcrumb = [
        ['title' => 'Quản lý rạp chiếu', 'url' => route('admin.theaters')],
        ['title' => 'Thêm rạp mới']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.theaters') }}" 
           class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Quay lại
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Thêm rạp chiếu mới</h1>
            <p class="text-gray-600 mt-1">Tạo rạp chiếu phim mới trong hệ thống</p>
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
    <form method="POST" action="{{ route('admin.theaters.store') }}" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i data-lucide="building-2" class="h-5 w-5"></i>
                Thông tin rạp chiếu
            </h3>
            
            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tên rạp chiếu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               placeholder="Nhập tên rạp chiếu" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Số điện thoại
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                               placeholder="Nhập số điện thoại">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Địa chỉ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="address" value="{{ old('address') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                           placeholder="Nhập địa chỉ rạp chiếu" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                           placeholder="Nhập email liên hệ">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mô tả
                    </label>
                    <textarea name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none"
                              placeholder="Nhập mô tả về rạp chiếu">{{ old('description') }}</textarea>
                </div>

                <!-- Status -->
                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-gray-700">
                        Trạng thái hoạt động
                    </label>
                    <div class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                               id="is_active-toggle">
                        <span class="toggle-slider"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác</h3>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.theaters') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="h-4 w-4"></i>
                    Hủy
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Tạo rạp chiếu
                </button>
            </div>
        </div>
    </form>

    <!-- Help Text -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i data-lucide="info" class="h-5 w-5 text-blue-600 mr-2 mt-0.5"></i>
            <div>
                <h4 class="font-medium text-blue-900 mb-1">Lưu ý</h4>
                <p class="text-blue-800 text-sm">
                    Các trường có dấu (*) là bắt buộc. Sau khi tạo rạp chiếu, bạn có thể chỉnh sửa thông tin bất kỳ lúc nào.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
