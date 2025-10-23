@extends('layouts.app')

@section('title', 'Quên mật khẩu - Phim Việt')
@section('description', 'Đặt lại mật khẩu tài khoản Phim Việt')

@section('content')
<div class="min-h-screen bg-background flex flex-col">
    <main class="flex-1 flex items-center justify-center px-4 py-8 lg:py-12">
        <div class="w-full max-w-md">
            <div class="rounded-lg border bg-card text-card-foreground shadow-lg border-0 bg-white/95 backdrop-blur-sm">
                <div class="flex flex-col space-y-1.5 p-6 space-y-1">
                    <h1 class="text-2xl text-center font-semibold leading-none tracking-tight">Quên mật khẩu</h1>
                    <p class="text-center text-sm text-muted-foreground">
                        Nhập email của bạn để nhận mã OTP đặt lại mật khẩu
                    </p>
                </div>
                <div class="p-6 pt-0">
                    <form method="POST" action="{{ route('forgot-password') }}" class="space-y-4">
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

                        @if(session('success'))
                            <div class="relative w-full rounded-lg border border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-green-600 dark:[&>svg]:text-green-400">
                                <div class="[&>svg~*]:pl-7">
                                    <div class="mb-4 ml-7 mt-4">
                                        <p class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                            {{ session('success') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Email</label>
                            <div class="relative">
                                <i data-lucide="mail" class="absolute left-3 top-3 h-4 w-4 text-muted-foreground"></i>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="your@email.com"
                                    value="{{ old('email') }}"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 pl-10 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    required
                                >
                            </div>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 w-full h-9">
                            Gửi mã OTP
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <span class="text-muted-foreground">Nhớ mật khẩu? </span>
                        <a href="{{ route('login') }}" class="text-primary hover:underline">
                            Đăng nhập ngay
                        </a>
                    </div>

                    <div class="mt-2 text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 h-8 px-3 text-muted-foreground">
                            <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i>
                            Quay lại đăng nhập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
