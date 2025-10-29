<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        try {
            $movieSlug = $request->get('movie');
            $theaterId = $request->get('theater');
            $date = $request->get('date', date('Y-m-d'));
            $timeSlot = $request->get('time', 'all');
            $scheduleId = $request->get('schedule');
            
            if (!$movieSlug) {
                return redirect()->route('home');
            }

            // Get movie by slug
            $movieResponse = $this->apiService->getMovieBySlug($movieSlug);
            if (!$movieResponse['success']) {
                return redirect()->route('home');
            }
            $movie = $movieResponse['data'];

            // Get all schedules for this movie
            $schedulesResponse = $this->apiService->getMovieSchedules($movie['id']);
            $allSchedules = [];
            
            if ($schedulesResponse['success'] && isset($schedulesResponse['data'])) {
                $allSchedules = $schedulesResponse['data'];
            } else {
                // Log error for debugging
                return redirect()->route('home')->with('error', 'Không thể tải lịch chiếu từ API: ' . ($schedulesResponse['message'] ?? 'Unknown error'));
            }

            // Get unique theaters from schedules
            $theaters = [];
            $theaterIds = [];
            
            foreach ($allSchedules as $schedule) {
                $theater = $schedule['theater'];
                
                if (!in_array($theater['id'], $theaterIds)) {
                    $theaterIds[] = $theater['id'];
                    
                    // Use theater data from schedules (already includes address)
                    $theaters[] = $theater;
                }
            }
            
            // Remove duplicates based on name and address
            $uniqueTheaters = [];
            $seenTheaters = [];
            
            foreach ($theaters as $theater) {
                $theaterKey = strtolower(trim($theater['name'] ?? '')) . '|' . strtolower(trim($theater['address'] ?? ''));
                
                if (!in_array($theaterKey, $seenTheaters)) {
                    $seenTheaters[] = $theaterKey;
                    $uniqueTheaters[] = $theater;
                }
            }
            
            $theaters = $uniqueTheaters;
            

            // Get selected theater
            $selectedTheater = null;
            if ($theaterId) {
                $selectedTheater = collect($theaters)->firstWhere('id', $theaterId);
            } else {
                // If no theater selected, select the first one
                $selectedTheater = !empty($theaters) ? $theaters[0] : null;
            }

            // Get available dates (next 7 days)
            $availableDates = [];
            foreach ($allSchedules as $schedule) {
                $scheduleDate = Carbon::parse($schedule['start_time'])->format('Y-m-d');
                if (!in_array($scheduleDate, $availableDates)) {
                    $availableDates[] = $scheduleDate;
                }
            }
            sort($availableDates);
            $availableDates = array_slice($availableDates, 0, 7);

            // Get time slots
            $timeSlots = [
                ['id' => 'all', 'label' => 'Tất cả', 'icon' => '🕐'],
                ['id' => 'morning', 'label' => 'Sáng (6h-12h)', 'icon' => '🌅'],
                ['id' => 'afternoon', 'label' => 'Chiều (12h-17h)', 'icon' => '☀️'],
                ['id' => 'evening', 'label' => 'Tối (17h-21h)', 'icon' => '🌆'],
                ['id' => 'night', 'label' => 'Đêm (21h-24h)', 'icon' => '🌙']
            ];

            // Filter schedules
            $schedules = [];
            if ($selectedTheater) {
                $schedules = collect($allSchedules)
                    ->filter(function($schedule) use ($selectedTheater, $date) {
                        $scheduleDate = Carbon::parse($schedule['start_time'])->format('Y-m-d');
                        return $schedule['theater_id'] == $selectedTheater['id'] && $scheduleDate == $date;
                    })
                    ->values()
                    ->toArray();

                // Filter out past showtimes
                $now = Carbon::now('Asia/Ho_Chi_Minh');$schedules = collect($schedules)
                    ->filter(function($schedule) use ($now) {
                        // Parse showtime - if it's stored as UTC, treat it as local time instead
                        $startTimeRaw = $schedule['start_time'];
                        
                        // If the time ends with 'Z', it's UTC but was meant to be local time
                        if (str_ends_with($startTimeRaw, 'Z')) {
                            // Remove the Z and parse as local time
                            $localTimeString = str_replace(['Z', '.000000'], '', $startTimeRaw);
                            $showtime = Carbon::createFromFormat('Y-m-d\TH:i:s', $localTimeString, 'Asia/Ho_Chi_Minh');
                        } else {
                            $showtime = Carbon::parse($startTimeRaw, 'Asia/Ho_Chi_Minh');
                        }
                        
                        $isPast = $showtime->lte($now);
                        return !$isPast;
                    })
                    ->values()
                    ->toArray();

                // Filter by time slot
                if ($timeSlot !== 'all') {
                    $schedules = collect($schedules)
                        ->filter(function($schedule) use ($timeSlot) {
                            $hour = Carbon::parse($schedule['start_time'])->hour;
                            switch ($timeSlot) {
                                case 'morning':
                                    return $hour < 12;
                                case 'afternoon':
                                    return $hour >= 12 && $hour < 17;
                                case 'evening':
                                    return $hour >= 17 && $hour < 21;
                                case 'night':
                                    return $hour >= 21;
                                default:
                                    return true;
                            }
                        })
                        ->values()
                        ->toArray();
                }
            }
            
            // Get selected schedule if any
            $selectedSchedule = null;
            if ($scheduleId) {
                $selectedSchedule = collect($schedules)->firstWhere('id', $scheduleId);
            }

            return view('booking.index', compact(
                'movie',
                'theaters',
                'selectedTheater',
                'selectedSchedule',
                'date',
                'timeSlot',
                'availableDates',
                'timeSlots',
                'schedules'
            ));

        } catch (\Exception $e) {return redirect()->route('home')->with('error', 'Không thể tải trang đặt vé: ' . $e->getMessage());
        }
    }

    public function seats(Request $request)
    {
        try {
            $scheduleId = $request->get('schedule');
            
            if (!$scheduleId) {
                return redirect()->route('home');
            }

            // Get schedule details
            $scheduleResponse = $this->apiService->getSchedule($scheduleId);
            if (!$scheduleResponse['success']) {
                return redirect()->route('home');
            }
            $schedule = $scheduleResponse['data'];

            // Get movie details
            $movieResponse = $this->apiService->getMovie($schedule['movie_id']);
            if (!$movieResponse['success']) {
                return redirect()->route('home');
            }
            $movie = $movieResponse['data'];

            // Get theater details from schedule (already included)
            $theater = $schedule['theater'] ?? null;
            if (!$theater) {
                return redirect()->route('home');
            }

            // Get seats for this schedule
            $seatsResponse = $this->apiService->getScheduleSeats($scheduleId);
            $seats = $seatsResponse['success'] ? $seatsResponse['data'] : [];

            return view('booking.seats', compact(
                'movie',
                'theater',
                'schedule',
                'seats'
            ));

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Không thể tải trang chọn ghế');
        }
    }

    public function checkout(Request $request)
    {
        try {
            $scheduleId = $request->get('schedule');
            $seatIds = $request->get('seats', []);
            
            if (!$scheduleId || empty($seatIds)) {
                return redirect()->route('home');
            }

            // Get schedule details
            $scheduleResponse = $this->apiService->getSchedule($scheduleId);
            if (!$scheduleResponse['success']) {
                return redirect()->route('home');
            }
            $schedule = $scheduleResponse['data'];

            // Get movie details
            $movieResponse = $this->apiService->getMovie($schedule['movie_id']);
            if (!$movieResponse['success']) {
                return redirect()->route('home');
            }
            $movie = $movieResponse['data'];

            // Get theater details from schedule (already included)
            $theater = $schedule['theater'] ?? null;
            if (!$theater) {
                return redirect()->route('home');
            }

            // Get selected seats
            $seatsResponse = $this->apiService->getScheduleSeats($scheduleId);
            $allSeats = $seatsResponse['success'] ? $seatsResponse['data'] : [];
            
            // Convert seat IDs to array if it's a string
            $seatIdArray = is_string($seatIds) ? explode(',', $seatIds) : $seatIds;
            
            $selectedSeats = collect($allSeats)->whereIn('seat_id', $seatIdArray)->values()->toArray();

            // Get snacks
            $snacksResponse = $this->apiService->getSnacks();
            $snacks = $snacksResponse['success'] ? $snacksResponse['data'] : [];

            // Get selected snacks from request parameters
            $selectedSnacks = [];
            $snacksTotal = 0;
            
            // Try to get snacks from request first, then from session
            $snacksParam = $request->get('snacks');
            if ($snacksParam) {
                $selectedSnacks = is_string($snacksParam) ? json_decode($snacksParam, true) : $snacksParam;
            }
            
            // Calculate snacks total
            if (!empty($selectedSnacks)) {
                foreach ($selectedSnacks as $snackId => $quantity) {
                    $snack = collect($snacks)->firstWhere('id', $snackId);
                    if ($snack && $quantity > 0) {
                        $snacksTotal += $snack['price'] * $quantity;
                    }
                }
            }
            
            // Calculate seats total
            $seatsTotal = count($selectedSeats) * $schedule['price'];
            
            // Calculate total price
            $totalPrice = $seatsTotal + $snacksTotal;

            // Get current user information
            $currentUser = $this->apiService->getCurrentUserFromSession();

            return view('booking.checkout', compact(
                'movie',
                'theater',
                'schedule',
                'selectedSeats',
                'snacks',
                'selectedSnacks',
                'seatsTotal',
                'snacksTotal',
                'totalPrice',
                'currentUser'
            ));

        } catch (\Exception $e) {return redirect()->route('home')->with('error', 'Không thể tải trang thanh toán');
        }
    }

    public function confirm(Request $request)
    {
        try {
            $scheduleId = $request->get('schedule');
            $seatIds = $request->get('seats', []);
            $customerName = $request->get('customer_name');
            $customerEmail = $request->get('customer_email');
            $customerPhone = $request->get('customer_phone');
            $totalPrice = $request->get('total_price');
            
            if (!$scheduleId || empty($seatIds) || !$customerName || !$customerEmail || !$customerPhone) {
                return redirect()->back()->with('error', 'Vui lòng điền đầy đủ thông tin');
            }

            // Convert seat IDs to array if it's a string
            $seatIdArray = is_string($seatIds) ? explode(',', $seatIds) : $seatIds;

            // Check seat availability first
            $seatsResponse = $this->apiService->getScheduleSeats($scheduleId);
            if (!$seatsResponse['success']) {
                return redirect()->back()->with('error', 'Không thể kiểm tra trạng thái ghế. Vui lòng thử lại.');
            }
            
            $allSeats = $seatsResponse['data'];
            $selectedSeatsData = collect($allSeats)->whereIn('seat_id', $seatIdArray);
            
            // Check if any selected seats are no longer available
            $unavailableSeats = $selectedSeatsData->whereIn('status', ['sold', 'reserved']);
            if ($unavailableSeats->isNotEmpty()) {
                $unavailableSeatNumbers = $unavailableSeats->map(function($seat) {
                    return $seat['row_label'] . $seat['seat_number'];
                })->implode(', ');
                
                return redirect()->route('booking.seats', ['schedule' => $scheduleId])
                    ->with('error', "Ghế {$unavailableSeatNumbers} đã được đặt bởi người khác. Vui lòng chọn ghế khác.");
            }

            // Get seat numbers for locking
            $seatNumbers = $this->getSeatNumbers($scheduleId, $seatIdArray);
            
            if (empty($seatNumbers)) {
                return redirect()->back()->with('error', 'Không tìm thấy thông tin ghế. Vui lòng thử lại.');
            }

            $lockResponse = $this->apiService->lockSeats([
                'schedule_id' => $scheduleId,
                'seat_numbers' => $seatNumbers,
                'lock_duration_minutes' => 10
            ]);if (!$lockResponse['success']) {// Parse error message to show user-friendly message
                $errorMessage = $lockResponse['message'] ?? 'Unknown error';
                if (str_contains($errorMessage, 'already reserved') || str_contains($errorMessage, 'already sold')) {
                    return redirect()->route('booking.seats', ['schedule' => $scheduleId])
                        ->with('error', 'Một số ghế đã được đặt bởi người khác. Vui lòng chọn ghế khác.');
                }
                
                return redirect()->back()->with('error', 'Không thể khóa ghế. Vui lòng thử lại.');
            }

            // Get snacks from request
            $snacksData = [];
            $snacksParam = $request->get('snacks');
            if ($snacksParam) {
                $snacks = is_string($snacksParam) ? json_decode($snacksParam, true) : $snacksParam;
                if (is_array($snacks)) {
                    foreach ($snacks as $snackId => $quantity) {
                        if ($quantity > 0) {
                            $snacksData[] = [
                                'snack_id' => $snackId,
                                'quantity' => $quantity
                            ];
                        }
                    }
                }
            }

            // Get current user info
            $user = session('user');
            $userId = $user['id'] ?? null;
            
            // Create booking
            $bookingData = [
                'showtime_id' => $scheduleId,
                'seat_ids' => $seatIdArray,
                'snacks' => $snacksData,
                'total_price' => $totalPrice,
                'user_id' => $userId // Add user_id to request
            ];

            $bookingResponse = $this->apiService->createBooking($bookingData);
            
            if ($bookingResponse['success']) {
                return redirect()->route('booking.success', ['booking' => $bookingResponse['data']['booking_id']]);
            } else {
                // Release locked seats if booking fails
                $this->apiService->releaseSeats([
                    'schedule_id' => $scheduleId,
                    'seat_ids' => $lockResponse['data']['seat_ids'] ?? []
                ]);
                
                return redirect()->back()->with('error', $bookingResponse['message'] ?? 'Có lỗi xảy ra khi đặt vé');
            }

        } catch (\Exception $e) {return redirect()->back()->with('error', 'Có lỗi xảy ra khi đặt vé');
        }
    }

    private function getSeatNumbers($scheduleId, $seatIds)
    {
        try {
            $seatsResponse = $this->apiService->getScheduleSeats($scheduleId);
            if (!$seatsResponse['success']) {
                return [];
            }

            $seats = $seatsResponse['data'];
            $seatNumbers = [];

            foreach ($seatIds as $seatId) {
                $seat = collect($seats)->firstWhere('seat_id', $seatId);
                if ($seat) {
                    $seatNumbers[] = $seat['row_label'] . $seat['seat_number'];
                }
            }

            return $seatNumbers;
        } catch (\Exception $e) {return [];
        }
    }

    public function snacks(Request $request)
    {
        try {
            $scheduleId = $request->get('schedule');
            $seatIds = $request->get('seats', []);
            
            if (!$scheduleId || empty($seatIds)) {
                return redirect()->route('home');
            }

            // Get schedule details
            $scheduleResponse = $this->apiService->getSchedule($scheduleId);
            if (!$scheduleResponse['success']) {
                return redirect()->route('home');
            }
            $schedule = $scheduleResponse['data'];

            // Get movie details
            $movieResponse = $this->apiService->getMovie($schedule['movie_id']);
            if (!$movieResponse['success']) {
                return redirect()->route('home');
            }
            $movie = $movieResponse['data'];

            // Get theater details from schedule (already included)
            $theater = $schedule['theater'] ?? null;
            if (!$theater) {
                return redirect()->route('home');
            }

            // Get selected seats
            $seatsResponse = $this->apiService->getScheduleSeats($scheduleId);
            $allSeats = $seatsResponse['success'] ? $seatsResponse['data'] : [];
            
            // Convert seat IDs to array if it's a string
            $seatIdArray = is_string($seatIds) ? explode(',', $seatIds) : $seatIds;
            
            $selectedSeats = collect($allSeats)->whereIn('seat_id', $seatIdArray)->values()->toArray();

            // Get snacks
            $snacksResponse = $this->apiService->getSnacks();
            $snacks = $snacksResponse['success'] ? $snacksResponse['data'] : [];

            // Calculate seats total
            $seatsTotal = count($selectedSeats) * $schedule['price'];

            return view('booking.snacks', compact(
                'movie',
                'theater',
                'schedule',
                'selectedSeats',
                'snacks',
                'seatsTotal'
            ));

        } catch (\Exception $e) {return redirect()->route('home')->with('error', 'Không thể tải trang chọn đồ ăn');
        }
    }

    public function success(Request $request)
    {
        $bookingId = $request->get('booking');
        
        if (!$bookingId) {
            return redirect()->route('home');
        }

        try {
            // Get booking details
            $bookingResponse = $this->apiService->getBookingDetails($bookingId);
            if (!$bookingResponse['success']) {
                return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đặt vé');
            }
            $booking = $bookingResponse['data'];

            return view('booking.success', compact('booking'));
        } catch (\Exception $e) {return redirect()->route('home')->with('error', 'Không thể tải thông tin đặt vé');
        }
    }
}
