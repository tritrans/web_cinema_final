@extends('layouts.admin')

@section('title', 'Báo cáo thống kê')

@php
    $breadcrumb = [
        ['title' => 'Báo cáo thống kê']
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-foreground">Báo cáo thống kê</h1>
            <p class="text-muted-foreground">Xem báo cáo và thống kê chi tiết của hệ thống</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center gap-2 px-4 py-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground rounded-md text-sm font-medium">
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                Làm mới
            </button>
            <button onclick="exportReport()" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground hover:bg-primary/90 rounded-md text-sm font-medium">
                <i data-lucide="download" class="h-4 w-4"></i>
                Xuất báo cáo
            </button>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Thống kê phim</h3>
                <i data-lucide="film" class="h-6 w-6 text-blue-600"></i>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Tổng số phim:</span>
                    <span class="font-medium">{{ $reportsData['movies_stats']['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Phim nổi bật:</span>
                    <span class="font-medium">{{ $reportsData['movies_stats']['featured'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Phim mới tháng này:</span>
                    <span class="font-medium">{{ $reportsData['movies_stats']['this_month'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Thống kê người dùng</h3>
                <i data-lucide="users" class="h-6 w-6 text-green-600"></i>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Tổng người dùng:</span>
                    <span class="font-medium">{{ $reportsData['users_stats']['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Đăng ký tháng này:</span>
                    <span class="font-medium">{{ $reportsData['users_stats']['this_month'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Người dùng hoạt động:</span>
                    <span class="font-medium">{{ $reportsData['users_stats']['active'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Thống kê đánh giá</h3>
                <i data-lucide="star" class="h-6 w-6 text-yellow-600"></i>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Tổng đánh giá:</span>
                    <span class="font-medium">{{ $reportsData['reviews_stats']['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Điểm trung bình:</span>
                    <span class="font-medium">{{ number_format($reportsData['reviews_stats']['average'] ?? 0, 1) }}/5</span>
                </div>
                <div class="flex justify-between">
                    <span>Đánh giá tháng này:</span>
                    <span class="font-medium">{{ $reportsData['reviews_stats']['this_month'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Thống kê đặt vé</h3>
                <i data-lucide="ticket" class="h-6 w-6 text-purple-600"></i>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Tổng vé đã bán:</span>
                    <span class="font-medium">{{ $reportsData['bookings_stats']['total'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Doanh thu tháng này:</span>
                    <span class="font-medium">{{ number_format($reportsData['bookings_stats']['revenue'] ?? 0) }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span>Vé hôm nay:</span>
                    <span class="font-medium">{{ $reportsData['bookings_stats']['today'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Placeholder -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Biểu đồ doanh thu theo tháng</h3>
            <div class="h-64 flex items-center justify-center bg-muted rounded-lg">
                @if(!empty($reportsData['monthly_revenue']))
                    <div class="w-full">
                        <div class="grid grid-cols-6 gap-2 h-48 items-end">
                            @foreach($reportsData['monthly_revenue'] as $month)
                                <div class="flex flex-col items-center">
                                    <div class="bg-primary rounded-t w-full" style="height: {{ $month['revenue'] > 0 ? max(20, ($month['revenue'] / max(array_column($reportsData['monthly_revenue'], 'revenue'))) * 150) : 5 }}px;"></div>
                                    <span class="text-xs text-muted-foreground mt-2">{{ $month['month'] }}</span>
                                    <span class="text-xs font-medium">{{ number_format($month['revenue']) }}đ</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <i data-lucide="bar-chart-3" class="h-12 w-12 text-muted-foreground mx-auto mb-2"></i>
                        <p class="text-muted-foreground">Không có dữ liệu doanh thu</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-card border border-border rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Phim được xem nhiều nhất</h3>
            <div class="h-64 overflow-y-auto">
                @if(!empty($reportsData['most_viewed_movies']))
                    <div class="space-y-3">
                        @foreach($reportsData['most_viewed_movies'] as $index => $movie)
                            <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-muted">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-sm font-medium">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm truncate">{{ $movie['title_vi'] ?? $movie['title'] }}</h4>
                                    <p class="text-xs text-muted-foreground">{{ $movie['reviews_count'] }} đánh giá • {{ number_format($movie['rating'], 1) }}/5</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-full flex items-center justify-center">
                        <div class="text-center">
                            <i data-lucide="film" class="h-12 w-12 text-muted-foreground mx-auto mb-2"></i>
                            <p class="text-muted-foreground">Không có dữ liệu phim</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Đang xuất...';
    button.disabled = true;
    
    // Create a temporary link to download the file
    const link = document.createElement('a');
    link.href = '{{ route("reports.export.public") }}';
    link.download = 'bao_cao_thong_ke_{{ date("Y-m-d") }}.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Reset button after a short delay
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}
</script>
@endpush
@endsection
