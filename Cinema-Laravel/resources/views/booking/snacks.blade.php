@extends('layouts.app')

@section('title', 'Chọn đồ ăn & thức uống - ' . ($movie['title'] ?? 'Phim'))
@section('description', 'Chọn đồ ăn và thức uống cho buổi xem phim')

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
                <h1 class="text-2xl font-bold">Chọn đồ ăn & thức uống</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Snacks Selection -->
                <div class="lg:col-span-2">
                    <!-- Movie Info -->
                    <div class="bg-card border rounded-lg shadow-sm mb-6">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold mb-4">Thông tin đặt vé</h2>
                            <div class="flex gap-4">
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
                        </div>
                    </div>

                    <!-- Snacks -->
                    <div class="bg-card border rounded-lg shadow-sm">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold mb-4">Đồ ăn & thức uống</h2>
                            <div class="space-y-4" id="snacks-container">
                                @if(count($snacks) > 0)
                                    @foreach($snacks as $snack)
                                        <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-accent/50 transition-colors">
                                            <div class="flex items-center gap-4">
                                                @if($snack['image'])
                                                    <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($snack['image']) }}" 
                                                         alt="{{ $snack['name'] }}" 
                                                         class="w-16 h-16 object-cover rounded-lg"
                                                         onerror="this.src='/images/placeholder-snack.svg'">
                                                @else
                                                    <div class="w-16 h-16 bg-muted rounded-lg flex items-center justify-center">
                                                        <i data-lucide="coffee" class="h-8 w-8 text-muted-foreground"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="font-semibold text-lg">{{ $snack['name'] }}</h4>
                                                    @if($snack['name_vi'] && $snack['name_vi'] !== $snack['name'])
                                                        <p class="text-sm text-muted-foreground">{{ $snack['name_vi'] }}</p>
                                                    @endif
                                                    @if($snack['description'])
                                                        <p class="text-sm text-muted-foreground mt-1">{{ $snack['description'] }}</p>
                                                    @endif
                                                    <p class="text-sm font-medium text-primary mt-1">
                                                        {{ number_format($snack['price']) }}đ
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <button onclick="changeSnackQuantity({{ $snack['id'] }}, -1)" 
                                                        class="w-8 h-8 rounded-full border border-input bg-background hover:bg-accent hover:text-accent-foreground flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                                        id="decrease-{{ $snack['id'] }}" disabled>
                                                    <i data-lucide="minus" class="h-4 w-4"></i>
                                                </button>
                                                <span class="w-8 text-center font-semibold" id="quantity-{{ $snack['id'] }}">0</span>
                                                <button onclick="changeSnackQuantity({{ $snack['id'] }}, 1)" 
                                                        class="w-8 h-8 rounded-full border border-input bg-background hover:bg-accent hover:text-accent-foreground flex items-center justify-center">
                                                    <i data-lucide="plus" class="h-4 w-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8 text-muted-foreground">
                                        <i data-lucide="coffee" class="h-12 w-12 mx-auto mb-4 opacity-50"></i>
                                        <p>Không có đồ ăn thức uống nào</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-card border rounded-lg shadow-sm sticky top-4">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Tóm tắt đơn hàng</h3>
                            
                            <!-- Seats -->
                            <div class="mb-4">
                                <h4 class="font-semibold mb-2">Ghế đã chọn</h4>
                                <div class="space-y-1">
                                    @foreach($selectedSeats as $seat)
                                        <div class="flex justify-between text-sm">
                                            <span>{{ $seat['row_label'] }}{{ $seat['seat_number'] }}</span>
                                            <span>{{ number_format($schedule['price']) }}đ</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="border-t pt-4 mb-4">
                                <div class="flex justify-between">
                                    <span>Ghế:</span>
                                    <span id="seats-total">{{ number_format($seatsTotal) }}đ</span>
                                </div>
                            </div>

                            <!-- Snacks -->
                            <div class="mb-4" id="snacks-summary" style="display: none;">
                                <h4 class="font-semibold mb-2">Đồ ăn & thức uống</h4>
                                <div class="space-y-1" id="snacks-list">
                                    <!-- Snacks will be populated here -->
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between">
                                        <span>Đồ ăn:</span>
                                        <span id="snacks-total">0đ</span>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between text-lg font-semibold">
                                    <span>Tổng cộng:</span>
                                    <span class="text-primary" id="total-price">{{ number_format($seatsTotal) }}đ</span>
                                </div>
                            </div>
                            
                            <button onclick="proceedToCheckout()" 
                                    class="w-full mt-6 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 font-semibold">
                                <i data-lucide="credit-card" class="h-4 w-4"></i>
                                Tiếp tục thanh toán
                            </button>
                            
                            <p class="text-xs text-muted-foreground text-center mt-3">
                                Bạn có thể bỏ qua bước này nếu không muốn đặt đồ ăn
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
let selectedSnacks = {};
let seatsTotal = {{ $seatsTotal }};
let snacks = @json($snacks);

function changeSnackQuantity(snackId, change) {
    const currentQuantity = selectedSnacks[snackId] || 0;
    const newQuantity = Math.max(0, currentQuantity + change);
    
    if (newQuantity === 0) {
        delete selectedSnacks[snackId];
    } else {
        selectedSnacks[snackId] = newQuantity;
    }
    
    updateSnackDisplay(snackId, newQuantity);
    updateOrderSummary();
}

function updateSnackDisplay(snackId, quantity) {
    const quantityElement = document.getElementById(`quantity-${snackId}`);
    const decreaseButton = document.getElementById(`decrease-${snackId}`);
    
    if (quantityElement) {
        quantityElement.textContent = quantity;
    }
    
    if (decreaseButton) {
        decreaseButton.disabled = quantity === 0;
    }
}

function updateOrderSummary() {
    const snacksSummary = document.getElementById('snacks-summary');
    const snacksList = document.getElementById('snacks-list');
    const snacksTotal = document.getElementById('snacks-total');
    const totalPrice = document.getElementById('total-price');
    
    const snackEntries = Object.entries(selectedSnacks);
    
    if (snackEntries.length > 0) {
        snacksSummary.style.display = 'block';
        
        // Update snacks list
        snacksList.innerHTML = '';
        let snacksTotalAmount = 0;
        
        snackEntries.forEach(([snackId, quantity]) => {
            const snack = snacks.find(s => s.id == snackId);
            if (snack) {
                const itemTotal = snack.price * quantity;
                snacksTotalAmount += itemTotal;
                
                const snackItem = document.createElement('div');
                snackItem.className = 'flex justify-between text-sm';
                snackItem.innerHTML = `
                    <span>${snack.name} x${quantity}</span>
                    <span>${new Intl.NumberFormat('vi-VN').format(itemTotal)}đ</span>
                `;
                snacksList.appendChild(snackItem);
            }
        });
        
        snacksTotal.textContent = new Intl.NumberFormat('vi-VN').format(snacksTotalAmount) + 'đ';
    } else {
        snacksSummary.style.display = 'none';
    }
    
    // Update total price
    const totalAmount = seatsTotal + (snackEntries.reduce((total, [snackId, quantity]) => {
        const snack = snacks.find(s => s.id == snackId);
        return total + (snack ? snack.price * quantity : 0);
    }, 0));
    
    totalPrice.textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + 'đ';
}

function proceedToCheckout() {
    // Store selected snacks in session storage
    sessionStorage.setItem('selectedSnacks', JSON.stringify(selectedSnacks));
    
    // Create snacks parameter for URL
    const snacksParam = encodeURIComponent(JSON.stringify(selectedSnacks));
    
    // Redirect to checkout with snacks data
    window.location.href = `/booking/checkout?schedule={{ $schedule['id'] }}&seats={{ collect($selectedSeats)->pluck('seat_id')->implode(',') }}&snacks=${snacksParam}`;
}

// Initialize display
document.addEventListener('DOMContentLoaded', function() {
    updateOrderSummary();
});
</script>
@endpush
