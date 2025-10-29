@extends('layouts.app')

@section('title', 'Thanh toán - ' . ($movie['title'] ?? 'Phim'))
@section('description', 'Thanh toán vé xem phim ' . ($movie['title'] ?? ''))

@section('content')
<div class="min-h-screen bg-background">
    <main class="container mx-auto px-4 py-8">
        @if(!$movie || !$schedule || empty($selectedSeats))
            <div class="text-center">
                <h1 class="text-2xl font-bold mb-4">Không tìm thấy thông tin đặt vé</h1>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                    Về trang chủ
                </a>
            </div>
        @else
            <!-- Error Messages -->
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-2 text-red-800">
                        <i data-lucide="alert-circle" class="h-5 w-5"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Header -->
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('booking.seats', ['schedule' => $schedule['id']]) }}" 
                   class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Quay lại
                </a>
                <h1 class="text-2xl font-bold">Thanh toán</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Booking Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-card border rounded-lg shadow-sm mb-6">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold mb-4">Thông tin đặt vé</h2>
                            
                            <!-- Movie Info -->
                            <div class="flex gap-4 mb-6">
                                <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($movie['poster']) }}" 
                                     alt="{{ $movie['title'] }}" 
                                     class="w-20 h-30 object-cover rounded-lg"
                                     onerror="this.src='/images/placeholder-movie.svg'">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg">{{ $movie['title'] }}</h3>
                                    <div class="space-y-1 text-sm text-muted-foreground mt-2">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="map-pin" class="h-4 w-4"></i>
                                            {{ $theater['name'] }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="ticket" class="h-4 w-4"></i>
                                            {{ $schedule['room_name'] }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar" class="h-4 w-4"></i>
                                            {{ \Carbon\Carbon::parse($schedule['start_time'])->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="clock" class="h-4 w-4"></i>
                                            {{ \Carbon\Carbon::parse($schedule['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule['end_time'])->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Seats -->
                            <div class="border-t pt-4">
                                <h4 class="font-semibold mb-3">Ghế đã chọn</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedSeats as $seat)
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-medium bg-primary/10 text-primary border-primary/20">
                                            {{ $seat['row_label'] }}{{ $seat['seat_number'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Selected Snacks -->
            @if(!empty($selectedSnacks))
                <div class="bg-card border rounded-lg shadow-sm mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold mb-4">Đồ ăn & thức uống đã chọn</h2>
                        <div class="space-y-3">
                            @foreach($selectedSnacks as $snackId => $quantity)
                                @php
                                    $snack = collect($snacks)->firstWhere('id', $snackId);
                                @endphp
                                @if($snack)
                                    <div class="flex items-center justify-between p-3 border rounded-lg">
                                        <div class="flex items-center gap-3">
                                            @if($snack['image'])
                                                <img src="{{ \App\Helpers\ImageHelper::getSnackImage($snack) }}" 
                                                     alt="{{ $snack['name'] }}" 
                                                     class="w-12 h-12 object-cover rounded"
                                                     onerror="this.src='/images/placeholder-snack.svg'">
                                            @else
                                                <div class="w-12 h-12 bg-muted rounded flex items-center justify-center">
                                                    <i data-lucide="coffee" class="h-6 w-6 text-muted-foreground"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="font-semibold">{{ $snack['name'] }}</h4>
                                                <p class="text-sm text-muted-foreground">Số lượng: {{ $quantity }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold">{{ number_format($snack['price'] * $quantity) }}đ</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Customer Information -->
            <div class="bg-card border rounded-lg shadow-sm">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Thông tin khách hàng</h2>
                    @if($currentUser)
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <i data-lucide="info" class="h-5 w-5 text-blue-500 mr-2"></i>
                                <p class="text-sm text-blue-700">
                                    Thông tin đã được tự động điền từ tài khoản của bạn. Bạn có thể chỉnh sửa nếu cần.
                                </p>
                            </div>
                        </div>
                    @endif
                            <form id="checkout-form" method="POST" action="{{ route('booking.confirm') }}">
                                @csrf
                                <input type="hidden" name="schedule" value="{{ $schedule['id'] }}">
                                <input type="hidden" name="seats" value="{{ collect($selectedSeats)->pluck('seat_id')->implode(',') }}">
                                <input type="hidden" name="total_price" value="{{ $totalPrice }}">
                                <input type="hidden" name="snacks" id="snacks-input" value="">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="customer_name" class="block text-sm font-medium mb-2">Họ và tên *</label>
                                        <input type="text" 
                                               id="customer_name" 
                                               name="customer_name" 
                                               required
                                               value="{{ $currentUser['name'] ?? '' }}"
                                               class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                               placeholder="Nhập họ và tên">
                                    </div>
                                    <div>
                                        <label for="customer_email" class="block text-sm font-medium mb-2">Email *</label>
                                        <input type="email" 
                                               id="customer_email" 
                                               name="customer_email" 
                                               required
                                               value="{{ $currentUser['email'] ?? '' }}"
                                               class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                               placeholder="Nhập email">
                                    </div>
                                    <div>
                                        <label for="customer_phone" class="block text-sm font-medium mb-2">Số điện thoại *</label>
                                        <input type="tel" 
                                               id="customer_phone" 
                                               name="customer_phone" 
                                               required
                                               value="{{ $currentUser['phone'] ?? '' }}"
                                               class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                               placeholder="Nhập số điện thoại">
                                    </div>
                                    <div>
                                        <label for="payment_method" class="block text-sm font-medium mb-2">Phương thức thanh toán</label>
                                        <select id="payment_method" 
                                                name="payment_method"
                                                class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                                            <option value="cash">Thanh toán tại rạp</option>
                                            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                                            <option value="momo">Ví MoMo</option>
                                            <option value="zalopay">Ví ZaloPay</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <label for="notes" class="block text-sm font-medium mb-2">Ghi chú (tùy chọn)</label>
                                    <textarea id="notes" 
                                              name="notes" 
                                              rows="3"
                                              class="w-full px-3 py-2 border border-input rounded-md bg-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                              placeholder="Nhập ghi chú nếu có"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-card border rounded-lg shadow-sm sticky top-4">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Tóm tắt đơn hàng</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Vé xem phim</span>
                                    <span>{{ count($selectedSeats) }} vé</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Giá vé</span>
                                    <span>{{ number_format($schedule['price']) }}đ/vé</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Ghế</span>
                                    <span>{{ collect($selectedSeats)->map(function($seat) { return $seat['row_label'] . $seat['seat_number']; })->implode(', ') }}</span>
                                </div>
                                @if(!empty($selectedSnacks))
                                    <div class="flex justify-between">
                                        <span class="text-muted-foreground">Đồ ăn & thức uống</span>
                                        <span>{{ count($selectedSnacks) }} món</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="border-t pt-4 mt-4">
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span>Ghế:</span>
                                        <span>{{ number_format($seatsTotal) }}đ</span>
                                    </div>
                                    @if(!empty($selectedSnacks) && $snacksTotal > 0)
                                        <div class="flex justify-between">
                                            <span>Đồ ăn:</span>
                                            <span>{{ number_format($snacksTotal) }}đ</span>
                                        </div>
                                    @endif
                                    <div class="border-t pt-2">
                                        <div class="flex justify-between text-lg font-semibold">
                                            <span>Tổng cộng</span>
                                            <span class="text-primary">{{ number_format($totalPrice) }}đ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" 
                                    form="checkout-form"
                                    class="w-full mt-6 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 font-semibold">
                                <i data-lucide="credit-card" class="h-4 w-4"></i>
                                Xác nhận đặt vé
                            </button>
                            
                            <p class="text-xs text-muted-foreground text-center mt-3">
                                Bằng việc đặt vé, bạn đồng ý với 
                                <a href="#" class="text-primary hover:underline">điều khoản sử dụng</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get snacks from session storage
    const selectedSnacks = JSON.parse(sessionStorage.getItem('selectedSnacks') || '{}');
    
    // Update snacks input
    document.getElementById('snacks-input').value = JSON.stringify(selectedSnacks);
    
    // Total price is already calculated by server, no need to recalculate
});

// Form validation
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const name = document.getElementById('customer_name').value.trim();
    const email = document.getElementById('customer_email').value.trim();
    const phone = document.getElementById('customer_phone').value.trim();
    
    if (!name || !email || !phone) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin bắt buộc');
        return;
    }
    
    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Vui lòng nhập email hợp lệ');
        return;
    }
    
    // Basic phone validation (Vietnamese phone number)
    const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
    if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
        e.preventDefault();
        alert('Vui lòng nhập số điện thoại hợp lệ');
        return;
    }
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Đang xử lý...';
});

// Auto-format phone number
document.getElementById('customer_phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('0')) {
        value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
    } else if (value.startsWith('84')) {
        value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{3})/, '+$1 $2 $3 $4');
    }
    e.target.value = value;
});
</script>
@endpush
