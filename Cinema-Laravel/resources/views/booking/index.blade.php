@extends('layouts.app')

@section('title', 'Đặt vé - ' . ($movie['title'] ?? 'Phim'))
@section('description', 'Đặt vé xem phim ' . ($movie['title'] ?? ''))

@section('content')
<div class="min-h-screen bg-background">
    <main class="container mx-auto px-4 py-8">
        @if(!$movie)
            <div class="text-center">
                <h1 class="text-2xl font-bold mb-4">Không tìm thấy phim</h1>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                    Về trang chủ
                </a>
            </div>
        @else
            <!-- Movie Info -->
            <div class="bg-card border rounded-lg shadow-sm mb-8">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-shrink-0">
                            <img src="{{ \App\Helpers\ImageHelper::getSafeImageUrl($movie['poster']) }}" 
                                 alt="{{ $movie['title'] }}" 
                                 class="w-32 h-48 object-cover rounded-lg"
                                 onerror="this.src='/images/placeholder-movie.svg'">
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold mb-2">{{ $movie['title'] }}</h1>
                            <p class="text-muted-foreground mb-4">{{ $movie['description'] }}</p>
                            <div class="flex flex-wrap gap-2 mb-4">
                                @if(isset($movie['genre']) && is_array($movie['genre']))
                                    @foreach($movie['genre'] as $genre)
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-input bg-background hover:bg-accent hover:text-accent-foreground">
                                            {{ $genre }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                            <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="clock" class="h-4 w-4"></i>
                                    {{ $movie['duration'] }} phút
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="calendar" class="h-4 w-4"></i>
                                    {{ date('Y', strtotime($movie['release_date'])) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Theater Selection -->
                <div class="lg:col-span-1">
                    <div class="bg-card border rounded-lg shadow-sm">
                        <div class="p-6">
                            <h2 class="text-lg font-semibold flex items-center gap-2 mb-4">
                                <i data-lucide="map-pin" class="h-5 w-5"></i>
                                Chọn rạp chiếu
                            </h2>
                            <div class="space-y-3">
                                @forelse($theaters as $theater)
                                    <div class="p-3 rounded-lg border cursor-pointer transition-colors {{ $selectedTheater && $selectedTheater['id'] == $theater['id'] ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/50' }}"
                                         onclick="selectTheater({{ $theater['id'] }})">
                                        <h3 class="font-semibold">{{ $theater['name'] ?? 'Rạp không xác định' }}</h3>
                                        @if(isset($theater['address']) && $theater['address'])
                                            <p class="text-sm text-muted-foreground">{{ $theater['address'] }}</p>
                                        @else
                                            <p class="text-sm text-muted-foreground">Địa chỉ không có sẵn</p>
                                        @endif
                                        @if(isset($theater['phone']) && $theater['phone'])
                                            <p class="text-sm text-muted-foreground">{{ $theater['phone'] }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-muted-foreground text-center py-4">Không có rạp chiếu nào</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Selection -->
                <div class="lg:col-span-2">
                    @if($selectedTheater)
                        <!-- Date Selection -->
                        <div class="bg-card border rounded-lg shadow-sm mb-6">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold flex items-center gap-2 mb-4">
                                    <i data-lucide="calendar" class="h-5 w-5"></i>
                                    Chọn ngày chiếu
                                </h2>
                                <div class="grid grid-cols-7 gap-2">
                                    @foreach($availableDates as $itemDate)
                                        @php
                                            $dateObj = new DateTime($itemDate);
                                            $isToday = $itemDate === date('Y-m-d');
                                            $isWeekend = $dateObj->format('N') >= 6;
                                        @endphp
                                        <button onclick="selectDate('{{ $itemDate }}')"
                                                class="text-xs h-16 flex flex-col items-center justify-center gap-1 rounded-md border {{ $itemDate === $date ? 'bg-primary text-primary-foreground border-primary' : 'border-input bg-background hover:bg-accent hover:text-accent-foreground' }} {{ $isWeekend ? 'text-pink-600' : '' }}">
                                            <div class="text-xs font-medium">
                                                {{ $isToday ? 'H.nay' : $dateObj->format('D') }}
                                            </div>
                                            <div class="text-lg font-bold {{ $isWeekend ? 'text-pink-600' : '' }}">
                                                {{ $dateObj->format('d') }}
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Time Slot Filter -->
                        <div class="bg-card border rounded-lg shadow-sm mb-6">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold flex items-center gap-2 mb-4">
                                    <i data-lucide="clock" class="h-5 w-5"></i>
                                    Chọn khung giờ
                                </h2>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                    @foreach($timeSlots as $slot)
                                        <button onclick="selectTimeSlot('{{ $slot['id'] }}')" 
                                                class="h-12 flex flex-col items-center justify-center gap-1 rounded-md border {{ $timeSlot === $slot['id'] ? 'bg-primary text-primary-foreground border-primary' : 'border-input bg-background hover:bg-accent hover:text-accent-foreground' }}">
                                            <div class="text-lg">{{ $slot['icon'] }}</div>
                                            <div class="text-xs font-medium">{{ $slot['label'] }}</div>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Selection -->
                        <div class="bg-card border rounded-lg shadow-sm">
                            <div class="p-6">
                                <h2 class="text-lg font-semibold flex items-center gap-2 mb-4">
                                    <i data-lucide="ticket" class="h-5 w-5"></i>
                                    Chọn suất chiếu - {{ \Carbon\Carbon::parse($date)->locale('vi')->isoFormat('dddd, DD MMMM YYYY') }}
                                </h2>
                                @if(count($schedules) > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($schedules as $schedule)
                                            <button onclick="{{ ($schedule['available_seats'] ?? 0) > 0 ? 'selectSchedule(' . $schedule['id'] . ')' : 'alert(\'Suất chiếu này đã hết ghế!\')' }}" 
                                                    class="relative h-28 w-full flex flex-col items-center justify-between p-3 rounded-lg border transition-all duration-200 {{ $selectedSchedule && $selectedSchedule['id'] == $schedule['id'] ? 'border-primary bg-primary/10 shadow-md' : 'border-input bg-background hover:bg-primary/5 hover:border-primary' }} {{ ($schedule['available_seats'] ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    {{ ($schedule['available_seats'] ?? 0) <= 0 ? 'disabled' : '' }}>
                                                @if($selectedSchedule && $selectedSchedule['id'] == $schedule['id'])
                                                    <div class="absolute top-2 right-2">
                                                        <i data-lucide="check-circle" class="h-4 w-4 text-primary"></i>
                                                    </div>
                                                @endif
                                                
                                                <!-- Two Column Layout -->
                                                <div class="flex justify-between items-start w-full h-full">
                                                    <!-- Left Column - Time & Room -->
                                                    <div class="flex flex-col items-start">
                                                        <div class="font-bold text-lg text-gray-900 leading-tight">
                                                            {{ \Carbon\Carbon::parse($schedule['start_time'])->format('H:i') }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 leading-tight">
                                                            - {{ \Carbon\Carbon::parse($schedule['end_time'])->format('H:i') }}
                                                        </div>
                                                        <div class="text-sm text-gray-600 mt-1 leading-tight">
                                                            {{ $schedule['room_name'] }}
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Right Column - Seats & Price -->
                                                    <div class="flex flex-col items-end text-right">
                                                        <div class="text-xs {{ ($schedule['available_seats'] ?? 0) > 10 ? 'text-green-600' : (($schedule['available_seats'] ?? 0) > 5 ? 'text-yellow-600' : 'text-red-600') }} leading-tight">
                                                            <i data-lucide="users" class="h-3 w-3 inline mr-1"></i>
                                                            @if(($schedule['available_seats'] ?? 0) <= 0)
                                                                <span class="font-semibold">Hết ghế</span>
                                                            @else
                                                                {{ $schedule['available_seats'] ?? 0 }}/{{ $schedule['total_seats'] ?? 0 }} ghế
                                                            @endif
                                                        </div>
                                                        @if(($schedule['available_seats'] ?? 0) > 0 && ($schedule['available_seats'] ?? 0) <= 5)
                                                            <div class="text-xs text-red-500 font-medium leading-tight">(Sắp hết)</div>
                                                        @endif
                                                        <div class="font-bold text-base text-primary mt-1 leading-tight">
                                                            {{ number_format($schedule['price']) }}đ
                                                        </div>
                                                    </div>
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-muted-foreground">
                                        <i data-lucide="users" class="h-12 w-12 mx-auto mb-4 opacity-50"></i>
                                        <p>Không có suất chiếu nào cho khung giờ này</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-card border rounded-lg shadow-sm">
                            <div class="py-12 text-center">
                                <i data-lucide="map-pin" class="h-12 w-12 mx-auto mb-4 text-muted-foreground opacity-50"></i>
                                <p class="text-muted-foreground">Vui lòng chọn rạp chiếu để xem suất chiếu</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </main>
</div>
@endsection

@push('scripts')
<script>
let selectedTheater = @json($selectedTheater ?? null);
let selectedDate = '{{ $date }}';
let selectedTimeSlot = '{{ $timeSlot }}';

function selectTheater(theaterId) {
    const url = new URL(window.location);
    url.searchParams.set('theater', theaterId);
    window.location.href = url.toString();
}

function selectDate(date) {
    const url = new URL(window.location);
    url.searchParams.set('date', date);
    window.location.href = url.toString();
}

function selectTimeSlot(timeSlot) {
    const url = new URL(window.location);
    url.searchParams.set('time', timeSlot);
    window.location.href = url.toString();
}

function selectSchedule(scheduleId) {
    window.location.href = `/booking/seats?schedule=${scheduleId}`;
}
</script>
@endpush
