@extends('layouts.app')

@section('title', 'Đặt vé thành công')
@section('description', 'Đặt vé thành công')

@section('content')
<div class="min-h-screen bg-background">
    <main class="container mx-auto px-4 py-8">
        @if(!$booking)
            <div class="text-center">
                <h1 class="text-2xl font-bold mb-4">Không tìm thấy thông tin đặt vé</h1>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                    Về trang chủ
                </a>
            </div>
        @else
            <div class="max-w-6xl mx-auto">
                <!-- Success Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">Đặt vé thành công!</h1>
                    <p class="text-muted-foreground">
                        Mã đặt vé của bạn: <span class="font-semibold text-primary">{{ $booking['booking_id'] ?? 'N/A' }}</span>
                    </p>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Ticket Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Movie Info Card -->
                        <div class="bg-white border rounded-lg shadow-sm">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                                    <i data-lucide="ticket" class="h-5 w-5"></i>
                                    Thông tin vé
                                </h2>
                                
                                <!-- Movie Info -->
                                <div class="flex gap-4 mb-4">
                                    <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($booking['movie']['poster'] ?? '') }}" 
                                         alt="{{ $booking['movie']['title'] ?? '' }}" 
                                         class="w-16 h-24 object-cover rounded-lg"
                                         onerror="this.src='/images/placeholder-movie.svg'">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg mb-2">{{ $booking['movie']['title'] ?? 'N/A' }}</h3>
                                        <div class="space-y-1 text-sm text-muted-foreground">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="map-pin" class="h-4 w-4"></i>
                                                {{ $booking['theater']['name'] ?? 'N/A' }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="calendar" class="h-4 w-4"></i>
                                                {{ isset($booking['showtime']['start_time']) ? \Carbon\Carbon::parse($booking['showtime']['start_time'])->format('l, d/m/Y') : 'N/A' }} - {{ isset($booking['showtime']['start_time']) ? \Carbon\Carbon::parse($booking['showtime']['start_time'])->format('H:i') : 'N/A' }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="users" class="h-4 w-4"></i>
                                                {{ $booking['showtime']['room_name'] ?? 'Phòng 1' }}
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="ticket" class="h-4 w-4"></i>
                                                {{ isset($booking['seats']) && is_array($booking['seats']) ? count($booking['seats']) : 0 }} ghế
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seats Card -->
                        <div class="bg-white border rounded-lg shadow-sm">
                            <div class="p-6">
                                <h3 class="font-semibold mb-3">Ghế đã đặt</h3>
                                <div class="flex flex-wrap gap-2">
                                    @if(isset($booking['seats']) && is_array($booking['seats']))
                                        @foreach($booking['seats'] as $seat)
                                            <span class="inline-flex items-center rounded-md border px-3 py-1 text-sm font-medium bg-blue-50 text-blue-700 border-blue-200">
                                                {{ $seat['seat_number'] ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted-foreground">Không có thông tin ghế</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Snacks Card -->
                        @if(isset($booking['snacks']) && is_array($booking['snacks']) && count($booking['snacks']) > 0)
                            <div class="bg-white border rounded-lg shadow-sm">
                                <div class="p-6">
                                    <h3 class="font-semibold mb-3">Đồ ăn & thức uống</h3>
                                    <div class="space-y-2">
                                        @foreach($booking['snacks'] as $snack)
                                            <div class="flex justify-between items-center">
                                                <span>{{ $snack['snack']['name'] ?? 'N/A' }} x{{ $snack['quantity'] ?? 1 }}</span>
                                                <span class="font-medium">{{ number_format($snack['total_price'] ?? 0) }}đ</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Important Notes -->
                        <div class="bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="p-6">
                                <h3 class="font-semibold text-amber-800 mb-3">Lưu ý quan trọng</h3>
                                <ul class="text-sm text-amber-700 space-y-1">
                                    <li>• Vui lòng đến rạp trước giờ chiếu 15 phút</li>
                                    <li>• Mang theo mã đặt vé và giấy tờ tùy thân</li>
                                    <li>• Vé không thể hoàn trả sau khi đã đặt</li>
                                    <li>• Liên hệ hotline nếu cần hỗ trợ</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - QR Code & Summary -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- QR Code Card -->
                        <div class="bg-white border rounded-lg shadow-sm">
                            <div class="p-6 text-center">
                                <h3 class="text-lg font-semibold mb-4 flex items-center justify-center gap-2">
                                    <i data-lucide="qr-code" class="h-5 w-5"></i>
                                    Mã QR vé
                                </h3>
                                <div class="flex justify-center mb-4">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($booking['booking_id'] ?? '') }}" 
                                         alt="QR Code" 
                                         class="w-48 h-48 border-2 border-gray-200 rounded-lg p-2 bg-white">
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    <strong>Mã đặt vé: {{ $booking['booking_id'] ?? 'N/A' }}</strong><br>
                                    Xuất trình mã QR này tại quầy để nhận vé
                                </div>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="bg-white border rounded-lg shadow-sm sticky top-4">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Tổng kết</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm">Mã đặt vé:</span>
                                        <span class="font-semibold">{{ $booking['booking_id'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm">Trạng thái:</span>
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                            Đã xác nhận
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm">Ngày đặt:</span>
                                        <span class="text-sm">{{ isset($booking['created_at']) ? \Carbon\Carbon::parse($booking['created_at'])->format('d/m/Y') : 'Hôm nay' }}</span>
                                    </div>
                                    <hr>
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Tổng cộng:</span>
                                        <span class="text-primary">{{ number_format($booking['total_price'] ?? 0) }}đ</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-3 mt-6">
                                    <button onclick="window.print()" 
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md border border-gray-300 bg-white hover:bg-gray-50 text-sm font-medium">
                                        <i data-lucide="download" class="h-4 w-4"></i>
                                        Tải vé PDF
                                    </button>
                                    <a href="{{ route('home') }}" 
                                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-white hover:bg-primary/90 text-sm font-medium">
                                        <i data-lucide="home" class="h-4 w-4"></i>
                                        Về trang chủ
                                    </a>
                                </div>
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
// Auto-scroll to top
window.scrollTo(0, 0);
</script>
@endpush
