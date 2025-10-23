@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - Phim Việt')
@section('description', 'Đặt lại mật khẩu với mã OTP')

@section('content')
<div class="min-h-screen bg-background flex flex-col">
    <main class="flex-1 flex items-center justify-center px-4 py-8 lg:py-12">
        <div class="w-full max-w-md">
            <div class="rounded-lg border bg-card text-card-foreground shadow-lg border-0 bg-white/95 backdrop-blur-sm">
                <div class="flex flex-col space-y-1.5 p-6 space-y-1">
                    <h1 class="text-2xl text-center font-semibold leading-none tracking-tight">Đặt lại mật khẩu</h1>
                    <p class="text-center text-sm text-muted-foreground">
                        Nhập mã OTP và mật khẩu mới
                    </p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('reset-password') }}" class="space-y-4">
                        @csrf
                        
                        @if($errors->any())
                            <div class="bg-destructive/15 text-destructive px-4 py-3 rounded-md">
                                <p class="text-sm">{{ $errors->first() }}</p>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-md">
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ $email }}"
                                class="input w-full bg-muted"
                                readonly
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="otp" class="text-sm font-medium">Mã OTP</label>
                            <input
                                id="otp"
                                name="otp"
                                type="text"
                                placeholder="Nhập mã OTP 6 số"
                                value="{{ old('otp') }}"
                                class="input w-full text-center text-lg tracking-widest"
                                maxlength="6"
                                required
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium">Mật khẩu mới</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Tối thiểu 6 ký tự"
                                    class="input w-full pr-10"
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
                            <label for="password_confirmation" class="text-sm font-medium">Xác nhận mật khẩu mới</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="Nhập lại mật khẩu mới"
                                class="input w-full"
                                required
                            >
                        </div>

                        <button type="submit" class="btn-primary w-full">
                            Đặt lại mật khẩu
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <span class="text-muted-foreground">Nhớ mật khẩu? </span>
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

// Auto-focus on OTP input
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    if (otpInput) {
        otpInput.focus();
    }
});
</script>
@endsection
