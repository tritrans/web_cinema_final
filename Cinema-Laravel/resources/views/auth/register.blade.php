@extends('layouts.app')

@section('title', 'Đăng ký - Phim Việt')
@section('description', 'Tạo tài khoản mới để xem phim Việt Nam')

@section('content')
<div class="min-h-screen bg-background flex flex-col">
    <main class="flex-1 flex items-center justify-center px-4 py-8 lg:py-12">
        <div class="w-full max-w-md">
            <div class="rounded-lg border bg-card text-card-foreground shadow-lg border-0 bg-white/95 backdrop-blur-sm">
                <div class="flex flex-col space-y-1.5 p-6 space-y-1">
                    <h1 class="text-2xl text-center font-semibold leading-none tracking-tight">Đăng ký</h1>
                    <p class="text-center text-sm text-muted-foreground">Tạo tài khoản mới để bắt đầu xem phim</p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        
                        @if($errors->any())
                            <div class="relative w-full rounded-lg border border-destructive/50 text-destructive dark:border-destructive [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-destructive">
                                <div class="[&>svg~*]:pl-7">
                                    <div class="mb-4 ml-7 mt-4">
                                        <p class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                            {{ $errors->first() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Họ và tên</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                placeholder="Nguyễn Văn A"
                                value="{{ old('name') }}"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="your@email.com"
                                value="{{ old('email') }}"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Mật khẩu</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Tối thiểu 8 ký tự"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    required
                                >
                                <button
                                    type="button"
                                    class="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                                    onclick="togglePassword('password')"
                                >
                                    <i data-lucide="eye" class="h-4 w-4" id="password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Xác nhận mật khẩu</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="Nhập lại mật khẩu"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                required
                            >
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 w-full h-9">
                            Đăng ký
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <span class="text-muted-foreground">Đã có tài khoản? </span>
                        <a href="{{ route('login') }}" class="text-primary hover:underline">
                            Đăng nhập ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const passwordIcon = document.getElementById(inputId + '-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.setAttribute('data-lucide', 'eye-off');
    } else {
        passwordInput.type = 'password';
        passwordIcon.setAttribute('data-lucide', 'eye');
    }
    
    // Re-initialize Lucide icons
    lucide.createIcons();
}
</script>
@endsection