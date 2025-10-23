@extends('layouts.app')

@section('title', 'Chọn ghế - ' . ($movie['title'] ?? 'Phim'))
@section('description', 'Chọn ghế xem phim ' . ($movie['title'] ?? ''))

@section('content')
<div class="min-h-screen bg-background">
    <main class="container mx-auto px-4 py-8">
        @if(!$movie || !$schedule)
            <div class="text-center">
                <h1 class="text-2xl font-bold mb-4">Không tìm thấy suất chiếu</h1>
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
                <a href="{{ route('booking.index', ['movie' => $movie['slug'] ?? '']) }}" 
                   class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Quay lại
                </a>
                <h1 class="text-2xl font-bold">Chọn ghế</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Movie Info -->
                <div class="lg:col-span-1">
                    <div class="bg-card border rounded-lg shadow-sm sticky top-4">
                        <div class="p-4">
                            <div class="space-y-4">
                                <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($movie['poster']) }}" 
                                     alt="{{ $movie['title'] }}" 
                                     class="w-full h-48 object-cover rounded-lg"
                                     onerror="this.src='/images/placeholder-movie.svg'">
                                <div>
                                    <h3 class="font-semibold text-lg">{{ $movie['title'] }}</h3>
                                    <div class="space-y-2 text-sm text-muted-foreground mt-2">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="map-pin" class="h-4 w-4"></i>
                                            {{ $theater['name'] }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="clock" class="h-4 w-4"></i>
                                            {{ \Carbon\Carbon::parse($schedule['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule['end_time'])->format('H:i') }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="ticket" class="h-4 w-4"></i>
                                            {{ $schedule['room_name'] }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar" class="h-4 w-4"></i>
                                            {{ \Carbon\Carbon::parse($schedule['start_time'])->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seat Selection -->
                <div class="lg:col-span-3">
                    <div class="bg-card border rounded-lg shadow-sm">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold flex items-center gap-2 mb-6">
                                <i data-lucide="users" class="h-5 w-5"></i>
                                Chọn ghế - {{ $schedule['room_name'] }}
                            </h2>

                            <!-- Screen -->
                            <div class="flex justify-center mb-8">
                                <div class="bg-gray-800 text-white py-3 px-12 rounded-lg text-center font-bold text-lg shadow-lg">
                                    MÀN HÌNH
                                </div>
                            </div>

                            <!-- Seats -->
                            <div class="flex justify-center">
                                <div class="space-y-2" id="seats-container">
                                    @if(count($seats) > 0)
                                        @php
                                            $seatsByRow = collect($seats)->groupBy('row_label');
                                        @endphp
                                        @foreach($seatsByRow as $rowLabel => $rowSeats)
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-6 text-center font-semibold text-sm text-gray-600">{{ $rowLabel }}</div>
                                                <div class="flex gap-1">
                                                    @foreach($rowSeats as $seat)
                                                        <button 
                                                            class="w-8 h-8 rounded text-xs font-semibold flex items-center justify-center cursor-pointer transition-colors seat-button {{ $seat['status'] === 'sold' ? 'bg-red-500 text-white cursor-not-allowed' : ($seat['status'] === 'reserved' ? 'bg-yellow-500 text-white cursor-not-allowed' : 'bg-gray-200 text-gray-700 hover:bg-gray-300') }}"
                                                            data-seat-id="{{ $seat['seat_id'] }}"
                                                            data-row="{{ $seat['row_label'] }}"
                                                            data-number="{{ $seat['seat_number'] }}"
                                                            data-status="{{ $seat['status'] }}"
                                                            onclick="selectSeat(this)"
                                                            {{ $seat['status'] === 'sold' || $seat['status'] === 'reserved' ? 'disabled' : '' }}>
                                                            {{ $seat['seat_number'] }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-8 text-muted-foreground">
                                            <i data-lucide="alert-circle" class="h-12 w-12 mx-auto mb-4 opacity-50"></i>
                                            <p>Không thể tải dữ liệu ghế</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="mt-8 flex justify-center gap-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-gray-200 rounded"></div>
                                    <span class="text-sm">Có thể chọn</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-primary rounded"></div>
                                    <span class="text-sm">Đã chọn</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                                    <span class="text-sm">Đã đặt</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                                    <span class="text-sm">Đã bán</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Summary -->
            <div id="booking-summary" class="mt-8 hidden">
                <div class="bg-card border rounded-lg shadow-sm">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <i data-lucide="shopping-cart" class="h-5 w-5"></i>
                                <div>
                                    <p class="font-semibold">
                                        <span id="selected-count">0</span> ghế đã chọn
                                    </p>
                                    <p class="text-sm text-muted-foreground" id="selected-seats">
                                        <!-- Selected seats will be shown here -->
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-primary" id="total-price">
                                    0đ
                                </p>
                                <button onclick="proceedToCheckout()" 
                                        class="mt-2 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                                    Tiếp tục
                                </button>
                            </div>
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
let selectedSeats = [];
let scheduleId = {{ $schedule['id'] ?? 0 }};
let seatPrice = {{ $schedule['price'] ?? 0 }};

function selectSeat(button) {
    const seatId = button.dataset.seatId;
    const row = button.dataset.row;
    const number = button.dataset.number;
    const status = button.dataset.status;
    
    if (status === 'sold' || status === 'reserved') {
        return;
    }
    
    const existingSeat = selectedSeats.find(seat => seat.seat_id == seatId);
    
    if (existingSeat) {
        // Remove seat
        selectedSeats = selectedSeats.filter(seat => seat.seat_id != seatId);
        button.classList.remove('bg-primary', 'text-primary-foreground');
        button.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
    } else {
        // Add seat
        selectedSeats.push({
            seat_id: seatId,
            row_label: row,
            seat_number: number,
            price: seatPrice
        });
        button.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
        button.classList.add('bg-primary', 'text-primary-foreground');
    }
    
    updateBookingSummary();
}

function updateBookingSummary() {
    const summaryElement = document.getElementById('booking-summary');
    const countElement = document.getElementById('selected-count');
    const seatsElement = document.getElementById('selected-seats');
    const priceElement = document.getElementById('total-price');
    
    if (selectedSeats.length > 0) {
        summaryElement.classList.remove('hidden');
        countElement.textContent = selectedSeats.length;
        seatsElement.textContent = selectedSeats.map(seat => `${seat.row_label}${seat.seat_number}`).join(', ');
        
        const totalPrice = selectedSeats.reduce((total, seat) => total + seat.price, 0);
        priceElement.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + 'đ';
    } else {
        summaryElement.classList.add('hidden');
    }
}

function proceedToCheckout() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất một ghế');
        return;
    }
    
    // Store selected seats in session storage
    sessionStorage.setItem('selectedSeats', JSON.stringify(selectedSeats));
    sessionStorage.setItem('schedule', JSON.stringify(@json($schedule)));
    
    // Redirect to snacks selection
    window.location.href = `/booking/snacks?schedule=${scheduleId}&seats=${selectedSeats.map(s => s.seat_id).join(',')}`;
}

// Auto-refresh seats every 30 seconds to show real-time availability
setInterval(function() {
    fetch(`/api/schedules/${scheduleId}/seats`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSeatStatuses(data.data);
            }
        })
        .catch(error => {
            console.error('Error refreshing seats:', error);
        });
}, 30000);

function updateSeatStatuses(seats) {
    seats.forEach(seat => {
        const button = document.querySelector(`[data-seat-id="${seat.seat_id}"]`);
        if (button) {
            const currentStatus = button.dataset.status;
            if (currentStatus !== seat.status) {
                button.dataset.status = seat.status;
                
                // Update button appearance
                button.classList.remove('bg-gray-200', 'bg-primary', 'bg-yellow-500', 'bg-red-500', 'text-gray-700', 'text-primary-foreground', 'text-white');
                
                if (seat.status === 'sold') {
                    button.classList.add('bg-red-500', 'text-white', 'cursor-not-allowed');
                    button.disabled = true;
                } else if (seat.status === 'reserved') {
                    button.classList.add('bg-yellow-500', 'text-white', 'cursor-not-allowed');
                    button.disabled = true;
                } else {
                    button.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                    button.disabled = false;
                }
                
                // Remove from selected if it was selected and now unavailable
                if (selectedSeats.find(s => s.seat_id == seat.seat_id) && seat.status !== 'available') {
                    selectedSeats = selectedSeats.filter(s => s.seat_id != seat.seat_id);
                    updateBookingSummary();
                }
            }
        }
    });
}
</script>
@endpush
