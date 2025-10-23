@extends('layouts.app')

@section('title', 'Vé của tôi - Phim Việt')
@section('description', 'Xem lịch sử đặt vé và quản lý vé của bạn')

@section('content')
<div class="min-h-screen bg-background">
    <main class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Quay lại
            </a>
            <h1 class="text-2xl font-bold">Vé của tôi</h1>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-border">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="filterTickets('all')" 
                            class="ticket-tab active py-2 px-1 border-b-2 border-primary text-primary font-medium text-sm">
                        Tất cả
                    </button>
                    <button onclick="filterTickets('upcoming')" 
                            class="ticket-tab py-2 px-1 border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:border-border font-medium text-sm">
                        Sắp chiếu
                    </button>
                    <button onclick="filterTickets('past')" 
                            class="ticket-tab py-2 px-1 border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:border-border font-medium text-sm">
                        Đã chiếu
                    </button>
                    <button onclick="filterTickets('cancelled')" 
                            class="ticket-tab py-2 px-1 border-b-2 border-transparent text-muted-foreground hover:text-foreground hover:border-border font-medium text-sm">
                        Đã hủy
                    </button>
                </nav>
            </div>
        </div>

        <!-- Bookings List -->
        @php
            $validBookings = array_filter($bookings, function($booking) {
                return is_array($booking);
            });
        @endphp
        
        @if(count($validBookings) > 0)
            <div class="space-y-4" id="bookings-container">
                @foreach($validBookings as $booking)
                    <div class="bg-card border rounded-lg shadow-sm hover:shadow-lg transition-shadow booking-item" 
                         data-status="{{ $booking['status'] ?? 'unknown' }}" 
                         data-showtime="{{ $booking['showtime']['start_time'] ?? '' }}">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-4">
                                <!-- Movie Poster -->
                                <div class="flex-shrink-0">
                                    <div class="w-24 h-36 bg-gray-200 rounded-lg flex items-center justify-center">
                                        @if(isset($booking['showtime']['movie']['poster']) || isset($booking['movie']['poster']))
                                            <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($booking['showtime']['movie']['poster'] ?? $booking['movie']['poster']) }}" 
                                                 alt="{{ $booking['showtime']['movie']['title'] ?? $booking['movie']['title'] ?? 'Movie' }}" 
                                                 class="w-full h-full object-cover rounded-lg"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        @endif
                                        <div class="text-center text-gray-500 text-xs {{ (isset($booking['showtime']['movie']['poster']) || isset($booking['movie']['poster'])) ? 'hidden' : '' }}">
                                            <div class="text-2xl mb-1">🎬</div>
                                            <div>No Image</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Booking Info -->
                                <div class="flex-1 space-y-3">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                        <div>
                                            <h3 class="font-semibold text-lg">
                                                {{ $booking['showtime']['movie']['title'] ?? $booking['movie']['title'] ?? 'Movie Title' }}
                                            </h3>
                                            <p class="text-sm text-muted-foreground">
                                                Mã vé: {{ $booking['booking_id'] }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($booking['status'] === 'confirmed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Đã xác nhận
                                                </span>
                                            @elseif($booking['status'] === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Chờ xác nhận
                                                </span>
                                            @elseif($booking['status'] === 'cancelled')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Đã hủy
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $booking['status'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="calendar" class="h-4 w-4 text-muted-foreground"></i>
                                                <span>
                                                    @if(isset($booking['showtime']['start_time']))
                                                        {{ \Carbon\Carbon::parse($booking['showtime']['start_time'])->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="clock" class="h-4 w-4 text-muted-foreground"></i>
                                                <span>
                                                    @if(isset($booking['showtime']['start_time']))
                                                        {{ \Carbon\Carbon::parse($booking['showtime']['start_time'])->format('H:i') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="map-pin" class="h-4 w-4 text-muted-foreground"></i>
                                                <span>{{ $booking['showtime']['theater']['name'] ?? $booking['theater']['name'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="users" class="h-4 w-4 text-muted-foreground"></i>
                                                <span>{{ $booking['showtime']['room_name'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Seats -->
                                    @if(isset($booking['seats']) && count($booking['seats']) > 0)
                                        <div>
                                            <p class="text-sm font-medium mb-1">Ghế đã đặt:</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($booking['seats'] as $seat)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-muted text-muted-foreground">
                                                        {{ $seat['seat_number'] ?? 'N/A' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Snacks -->
                                    @if(isset($booking['snacks']) && count($booking['snacks']) > 0)
                                        <div>
                                            <p class="text-sm font-medium mb-1">Đồ ăn & thức uống:</p>
                                            <div class="space-y-1">
                                                @foreach($booking['snacks'] as $item)
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span>{{ $item['snack']['name'] ?? 'N/A' }} x{{ $item['quantity'] ?? 1 }}</span>
                                                        <span class="font-medium text-primary">
                                                            {{ number_format($item['total_price'] ?? 0) }}đ
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Total Price -->
                                    <div class="flex items-center justify-between pt-2 border-t">
                                        <span class="font-semibold">Tổng cộng:</span>
                                        <span class="text-lg font-bold text-primary">
                                            {{ number_format($booking['total_price'] ?? 0) }}đ
                                        </span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('booking.success', ['booking' => $booking['booking_id']]) }}" 
                                       class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground text-sm">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                        Xem chi tiết
                                    </a>
                                    <button onclick="downloadTicket('{{ $booking['booking_id'] }}')" 
                                            class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground text-sm">
                                        <i data-lucide="download" class="h-4 w-4"></i>
                                        Tải vé
                                    </button>
                                    @if($booking['status'] === 'confirmed' && isset($booking['showtime']['start_time']) && \Carbon\Carbon::parse($booking['showtime']['start_time'])->isFuture())
                                        <button onclick="cancelBooking('{{ $booking['booking_id'] }}')" 
                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 text-sm">
                                            <i data-lucide="trash-2" class="h-4 w-4"></i>
                                            Hủy vé
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-card border rounded-lg shadow-sm">
                <div class="py-12 text-center">
                    <i data-lucide="ticket" class="h-12 w-12 mx-auto mb-4 text-muted-foreground opacity-50"></i>
                    <h3 class="text-lg font-semibold mb-2">Chưa có vé nào</h3>
                    <p class="text-muted-foreground mb-4">
                        @if(count($bookings) > 0)
                            Có dữ liệu nhưng không hợp lệ. Vui lòng thử lại sau.
                        @else
                            Bạn chưa đặt vé nào. Hãy khám phá các bộ phim và đặt vé ngay!
                        @endif
                    </p>
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                        Đặt vé ngay
                    </a>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection

@push('scripts')
<script>
let currentFilter = 'all';

function filterTickets(filter) {
    currentFilter = filter;
    
    // Update tab styles
    document.querySelectorAll('.ticket-tab').forEach(tab => {
        tab.classList.remove('active', 'border-primary', 'text-primary');
        tab.classList.add('border-transparent', 'text-muted-foreground');
    });
    
    event.target.classList.add('active', 'border-primary', 'text-primary');
    event.target.classList.remove('border-transparent', 'text-muted-foreground');
    
    // Filter bookings
    const bookings = document.querySelectorAll('.booking-item');
    const now = new Date();
    
    bookings.forEach(booking => {
        const status = booking.dataset.status;
        const showtime = booking.dataset.showtime;
        const showtimeDate = showtime ? new Date(showtime) : null;
        
        let shouldShow = false;
        
        switch(filter) {
            case 'all':
                shouldShow = true;
                break;
            case 'upcoming':
                // Sắp chiếu: vé đã xác nhận và chưa tới giờ bắt đầu chiếu
                shouldShow = status === 'confirmed' && showtimeDate && showtimeDate > now;
                break;
            case 'past':
                // Đã chiếu: đã qua giờ bắt đầu chiếu (bất kể trạng thái, trừ đã hủy)
                shouldShow = status !== 'cancelled' && showtimeDate && showtimeDate <= now;
                break;
            case 'cancelled':
                // Đã hủy: vé bị hủy
                shouldShow = status === 'cancelled';
                break;
        }
        
        booking.style.display = shouldShow ? 'block' : 'none';
    });
}

function downloadTicket(bookingId) {
    // In a real app, this would generate and download a PDF ticket
    alert('Tính năng tải vé sẽ được phát triển');
}

function cancelBooking(bookingId) {
    if (!confirm('Bạn có chắc chắn muốn hủy vé này?')) {
        return;
    }
    
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Đang xử lý...';
    button.disabled = true;
    
    fetch(`/api/bookings/${bookingId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Hủy vé thành công!');
            location.reload();
        } else {
            throw new Error(data.message || 'Hủy vé thất bại');
        }
    })
    .catch(error => {
        alert('Lỗi: ' + error.message);
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

// Initialize display
document.addEventListener('DOMContentLoaded', function() {
    filterTickets('all');
});
</script>
@endpush
