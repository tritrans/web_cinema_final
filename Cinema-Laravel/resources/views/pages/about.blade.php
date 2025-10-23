@extends('layouts.app')

@section('title', 'Về chúng tôi - Phim Việt')
@section('description', 'Tìm hiểu về Phim Việt - nền tảng streaming hàng đầu dành riêng cho điện ảnh Việt Nam')

@section('content')
<div class="min-h-screen">
    <main class="flex-1">
        <div class="container mx-auto px-4 py-8">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h1 class="text-5xl font-bold text-foreground mb-6">
                    Về <span class="text-primary">Phim Việt</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-3xl mx-auto leading-relaxed">
                    Chúng tôi là nền tảng streaming hàng đầu dành riêng cho điện ảnh Việt Nam, mang đến cho bạn những tác phẩm
                    điện ảnh đặc sắc từ quê hương.
                </p>
            </div>

            <!-- Team Section -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-8">Đội ngũ của chúng tôi</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($team as $member)
                        <div class="card text-center hover:shadow-lg transition-shadow">
                            <div class="card-content p-6">
                                <img src="{{ $member['avatar'] }}" alt="{{ $member['name'] }}" class="w-20 h-20 rounded-full mx-auto mb-4 object-cover">
                                <h3 class="text-xl font-semibold mb-2 text-foreground">{{ $member['name'] }}</h3>
                                <div class="badge bg-secondary text-secondary-foreground mb-3">
                                    {{ $member['role'] }}
                                </div>
                                <p class="text-muted-foreground text-sm leading-relaxed">{{ $member['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Features Section -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-center mb-8">Tại sao chọn Phim Việt?</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    @foreach($features as $feature)
                        <div class="card hover:shadow-lg transition-shadow">
                            <div class="card-content p-6">
                                <h3 class="text-xl font-semibold mb-3 text-foreground">{{ $feature['title'] }}</h3>
                                <p class="text-muted-foreground leading-relaxed">{{ $feature['description'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Vision Section -->
            <div class="text-center">
                <div class="card bg-gradient-to-r from-secondary/10 to-primary/10 border-secondary/20">
                    <div class="card-content p-8">
                        <h2 class="text-3xl font-bold mb-6">Tầm nhìn tương lai</h2>
                        <p class="text-lg text-muted-foreground max-w-4xl mx-auto leading-relaxed mb-6">
                            Chúng tôi hướng tới việc trở thành nền tảng streaming số 1 cho điện ảnh Việt Nam, không chỉ tại Việt
                            Nam mà còn trên toàn thế giới. Chúng tôi sẽ tiếp tục mở rộng bộ sưu tập, cải thiện trải nghiệm người
                            dùng và xây dựng cộng đồng yêu phim mạnh mẽ.
                        </p>
                        <div class="badge text-lg px-4 py-2">
                            Điện ảnh Việt - Tự hào dân tộc
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
