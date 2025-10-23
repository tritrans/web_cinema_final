@extends('layouts.app')

@section('title', 'Đăng nhập - Phim Việt')
@section('description', 'Đăng nhập vào tài khoản Phim Việt để xem phim')

@section('content')
<div class="min-h-screen bg-background flex flex-col">
    <main class="flex-1 flex items-center justify-center px-4 py-8 lg:py-12">
        <div class="w-full max-w-md">
            <div class="rounded-lg border bg-card text-card-foreground shadow-lg border-0 bg-white/95 backdrop-blur-sm">
                <div class="flex flex-col space-y-1.5 p-6 space-y-1">
                    <h1 class="text-2xl text-center font-semibold leading-none tracking-tight">Đăng nhập</h1>
                    <p class="text-center text-sm text-muted-foreground">
                        Nhập email và mật khẩu để truy cập tài khoản của bạn
                    </p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
                                    placeholder="Nhập mật khẩu"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    required
                                >
                                <button
                                    type="button"
                                    class="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                                    onclick="togglePassword()"
                                >
                                    <i data-lucide="eye" class="h-4 w-4" id="password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 w-full h-9">
                            Đăng nhập
                        </button>
                    </form>

                    <div class="mt-4 text-center">
                        <a href="{{ route('forgot-password') }}" class="text-sm text-primary hover:underline">
                            Quên mật khẩu?
                        </a>
                    </div>

                    <div class="mt-6 text-center text-sm">
                        <span class="text-muted-foreground">Chưa có tài khoản? </span>
                        <a href="{{ route('register') }}" class="text-primary hover:underline">
                            Đăng ký ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
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