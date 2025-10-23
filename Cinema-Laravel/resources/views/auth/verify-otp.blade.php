@extends('layouts.app')

@section('title', 'Xác thực Email - Phim Việt')
@section('description', 'Xác thực email để hoàn tất đăng ký')

@section('content')
<div class="min-h-screen bg-background flex flex-col">
    <main class="flex-1 flex items-center justify-center px-4 py-8 lg:py-12">
        <div class="w-full max-w-md">
            <div class="rounded-lg border bg-card text-card-foreground shadow-lg border-0 bg-white/95 backdrop-blur-sm">
                <div class="flex flex-col space-y-1.5 p-6 space-y-1">
                    <h1 class="text-2xl text-center font-semibold leading-none tracking-tight">Xác thực Email</h1>
                    <p class="text-center text-sm text-muted-foreground">
                        Chúng tôi đã gửi mã xác thực đến email của bạn
                    </p>
                    <p class="text-center text-sm font-medium text-foreground">
                        {{ $email }}
                    </p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('verify-otp') }}" class="space-y-4">
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

                        @if(session('warning'))
                            <div class="bg-yellow-100 text-yellow-700 px-4 py-3 rounded-md">
                                <p class="text-sm">{{ session('warning') }}</p>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label for="otp" class="text-sm font-medium">Mã xác thực (OTP)</label>
                            <input
                                id="otp"
                                name="otp"
                                type="text"
                                placeholder="Nhập 6 chữ số"
                                value="{{ old('otp') }}"
                                class="input w-full text-center text-lg tracking-widest"
                                maxlength="6"
                                required
                            >
                        </div>

                        <input type="hidden" name="email" value="{{ $email }}">
                        <input type="hidden" name="name" value="{{ $name }}">
                        <input type="hidden" name="password" value="{{ $password }}">
                        <input type="hidden" name="password_confirmation" value="{{ $passwordConfirmation }}">

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Xác thực
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <button type="button" class="text-blue-600 hover:underline flex items-center justify-center mx-auto" onclick="resendOtp()">
                            <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
                            Gửi lại mã OTP
                        </button>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-muted-foreground hover:underline">
                            Đã có tài khoản? Đăng nhập ngay
                        </a>
                    </div>

                    <div class="mt-2 text-center">
                        <a href="{{ route('register') }}" class="text-sm text-muted-foreground hover:underline flex items-center justify-center">
                            <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i>
                            Quay lại đăng ký
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Auto-focus on OTP input
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    if (otpInput) {
        otpInput.focus();
    }
});

function resendOtp() {
    // This would typically make an AJAX request to resend OTP
    alert('Chức năng gửi lại OTP đang được phát triển');
}
</script>
@endsection
