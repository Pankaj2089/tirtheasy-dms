<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\Orders;
use App\Models\OrderPayments;
use App\Models\RoomPrices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
    private static $HotelsModel;
    private static $OrdersModel;
    private static $OrderPaymentsModel;
    private static $RoomPricesModel;

    public function __construct()
    {
        self::$HotelsModel = new Hotels();
        self::$OrdersModel = new Orders();
        self::$OrderPaymentsModel = new OrderPayments();
        self::$RoomPricesModel = new RoomPrices();
    }

    public function index(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return redirect('/');
        }

        $vendorId = $request->session()->get('vendor_id');
        $hotels = $this->getVendorHotels($vendorId);
        
        // Get filter date from request
        $filterDateInput = $request->query('filter_date');
        $useFilterDate = !empty($filterDateInput) && trim($filterDateInput) !== '';
        
        // Parse filter date if provided, otherwise set to null
        $filterDate = null;
        $filterDateStr = null;
        if ($useFilterDate) {
            try {
                $filterDate = Carbon::parse(trim($filterDateInput))->startOfDay();
                $filterDateStr = $filterDate->format('Y-m-d');
            } catch (\Exception $e) {
                // If date parsing fails, treat as no filter
                $useFilterDate = false;
                $filterDate = null;
            }
        }
        
        // Get bookings data
        $bookings = $this->getBookingsForReservation($vendorId);
        
        // Separate bookings by status
        $reservations = [];
        $arrivals = [];
        $departures = [];
        $inHouse = [];
        
        $today = Carbon::today();
        $now = Carbon::now();
        foreach ($bookings as $booking) {
            // Skip if check_in_date is missing or invalid
            if (empty($booking['check_in_date'])) {
                continue;
            }
            
            try {
                // Parse and normalize check-in date to ensure consistent format
                $checkInDate = Carbon::parse($booking['check_in_date'])->startOfDay();
                $checkInDateStr = $checkInDate->format('Y-m-d'); // Normalize to Y-m-d format
            } catch (\Exception $e) {
                // Skip booking if date can't be parsed
                continue;
            }
            
            // Note: We don't do early exit here because different tabs need different date filters:
            // - Arrivals: filter by check-in date (STRICT - exact match required)
            // - Departures: filter by check-out date  
            // - In-House: filter by stay period (check-in <= filter <= check-out)
            
            $checkOutDate = null;
            if (!empty($booking['check_out_date'])) {
                try {
                    $checkOutDate = Carbon::parse($booking['check_out_date'])->startOfDay();
                } catch (\Exception $e) {
                    // Continue without check-out date if it can't be parsed
                    $checkOutDate = null;
                }
            }
            
            // Parse check-in time
            $checkInTime = $booking['check_in_time'] ?? '14:00';
            try {
                $checkInDateTime = Carbon::parse($booking['check_in_date'] . ' ' . $checkInTime);
            } catch (\Exception $e) {
                // Fallback to just the date if time parsing fails
                $checkInDateTime = $checkInDate->copy()->setTime(14, 0);
            }
            
            // Calculate payment totals
            $paymentData = $this->getBookingPaymentData($booking['id']);
            
            $bookingData = [
                'id' => 'ORD-' . $booking['id'],
                'order_id' => $booking['id'],
                'guest_name' => $booking['billing_name'] ?? 'Guest',
                'reservation_number' => $booking['invoice_id'] ?? 'ORD-' . $booking['id'],
                'check_in_date' => $booking['check_in_date'],
                'check_out_date' => $booking['check_out_date'],
                'check_in_time' => $booking['check_in_time'] ?? '14:00',
                'check_out_time' => $booking['check_out_time'] ?? '12:00',
                'nights' => $this->calculateNights($booking['check_in_date'], $booking['check_out_date']),
                'rooms' => $booking['rooms'] ?? 1,
                'adults' => $booking['adults'] ?? 0,
                'children' => $booking['childs'] ?? 0,
                'reservation_type' => $booking['reservation_type'] ?? 'confirmed',
                'room_number_ids' => $booking['room_number_ids'],
                'has_room_assigned' => $booking['has_room_assigned'] ?? false,
                'total' => $paymentData['grand_total'],
                'paid' => $paymentData['paid'],
                'balance' => $paymentData['balance'],
                'payment_status' => $booking['payment_status'] ?? 'PENDING'
            ];
            
            // Check if guest has checked in (has room assigned AND check-in date is today or past)
            // For future dates, even if room is assigned, guest hasn't checked in yet
            $hasRoomAssigned = $booking['has_room_assigned'] ?? false;
            $hasCheckedIn = $hasRoomAssigned && ($checkInDate->isToday() || $checkInDate->isPast());
            
            // Arrivals: Show bookings based on check-in date - STRICT FILTERING WITH TIME CHECK
            $todayStr = $today->format('Y-m-d');
            
            if ($useFilterDate && $filterDate !== null) {
                // STRICT FILTERING: Only show bookings with check-in date exactly matching selected date
                // CRITICAL: Reject any booking with check-in date different from selected date
                // This prevents future dates (30, 31) from showing when 29 is selected
                
                // First check: Exact string match - MUST match exactly
                if ($checkInDateStr !== $filterDateStr) {
                    // Skip this booking - check-in date doesn't match selected date
                    // This prevents future dates (30/12, 31/12) from appearing when 29/12 is selected
                    // Do not add to arrivals - continue to next booking
                } elseif ($checkInDate->isSameDay($filterDate)) {
                    // Second check: Carbon date comparison confirms exact match
                    // TIME-BASED CONDITION: Only show if current time is BEFORE midnight (12:00 AM) of selected date
                    // Condition: booking.checkInDate === selectedDate AND currentTime < startOfSelectedDate (12:00 AM)
                    // Get midnight (start of day) of the selected filter date
                    $filterDateMidnight = $filterDate->copy()->startOfDay(); // This is 00:00:00 of selected date
                    
                    // Check if current time is BEFORE midnight of the selected date
                    $currentTime = Carbon::now();
                    $isBeforeMidnight = $currentTime->lt($filterDateMidnight);
                    
                    if ($isBeforeMidnight) {
                        // ✅ Show booking - current time is before midnight of check-in date
                        // Example: Selected 29/12/2025, Current time 11:30 PM (28/12/2025) → Show
                        $arrivals[] = $bookingData;
                    }
                    // ❌ If current time >= midnight of selected date, don't show (booking has "arrived")
                    // Example: Selected 29/12/2025, Current time 12:01 AM (29/12/2025) → Don't show
                }
                // If dates don't match, booking is skipped (no future dates shown)
            } else {
                // Default behavior when no filter date is selected
                // Show only today's bookings that haven't checked in yet AND current time is before midnight of today
                $todayMidnight = $today->copy()->startOfDay(); // Midnight of today
                $currentTime = Carbon::now();
                $isBeforeTodayMidnight = $currentTime->lt($todayMidnight);
                
                // Only show if:
                // 1. Check-in date is today
                // 2. Guest hasn't checked in yet
                // 3. Current time is before midnight of today
                if ($checkInDateStr === $todayStr && !$hasCheckedIn && $isBeforeTodayMidnight) {
                    $arrivals[] = $bookingData;
                }
                // Don't show future dates when no filter is selected
            }
            
            
            // Departures: Check-out date matches filter date (or today if no filter)
            if ($useFilterDate) {
                // Since filter is active, only show if check-out date matches filter date
                if ($checkOutDate && $checkOutDate->isSameDay($filterDate)) {
                    $departures[] = $bookingData;
                }
            } else {
                if ($checkOutDate && $checkOutDate->isToday()) {
                    $departures[] = $bookingData;
                }
            }
            
            // In-House: Only show guests with checkin_checkout_status = 'checked_in'
            // AND check_out_date is NOT today (or NOT filter date if filter is applied)
            $checkinCheckoutStatus = $booking['checkin_checkout_status'] ?? null;
            
            if ($checkinCheckoutStatus === 'checked_in') {
                if ($useFilterDate) {
                    // Filter by selected date
                    // Include if: check_out_date doesn't match filter date (or is null/empty)
                    if (!$checkOutDate || !$checkOutDate->isSameDay($filterDate)) {
                        $inHouse[] = $bookingData;
                    }
                } else {
                    // Default: Only include if check_out_date is not today
                    if (!$checkOutDate || !$checkOutDate->isToday()) {
                        $inHouse[] = $bookingData;
                    }
                }
            }
            
            // Reservations: Only future bookings (check-in date is in the future)
            // OR bookings that haven't arrived yet (check-in is today but check-in time hasn't come yet)
            if ($useFilterDate) {
                // Since we already filtered above, all bookings here have matching check-in date
                // If filter date is today, check if check-in time hasn't passed
                if ($filterDate->isToday()) {
                    if ($checkInDateTime->isFuture()) {
                        $reservations[] = $bookingData;
                    }
                } else {
                    // Filter date is in the future, show all bookings for that date
                    $reservations[] = $bookingData;
                }
            } else {
                // Default: Show all future bookings (check-in date is in the future)
                // OR bookings that haven't arrived yet (check-in is today but check-in time hasn't come yet)
                if ($checkInDate->isFuture()) {
                    $reservations[] = $bookingData;
                } elseif ($checkInDate->isToday() && $checkInDateTime->isFuture()) {
                    // Check-in is today but check-in time hasn't arrived yet
                    $reservations[] = $bookingData;
                }
            }
        }
        
        $siteUrl = env('APP_URL');
        $selectedFilterDate = $filterDateInput ? $filterDate->format('Y-m-d') : date('Y-m-d');
        
        // Get quick reservation rooms data (same as calendar page)
        $quickReservationRooms = $this->buildQuickReservationRooms($vendorId);
        
        return view('reservation.index', compact('reservations', 'arrivals', 'departures', 'inHouse', 'hotels', 'siteUrl', 'selectedFilterDate', 'quickReservationRooms'));
    }

    private function getVendorHotels($vendorId)
    {
        $hotels = self::$HotelsModel
            ->where('vendor_id', $vendorId)
            ->where('status', 1)
            ->get(['id', 'title']);
        
        return $hotels;
    }

    private function getBookingsForReservation($vendorId)
    {
        $orders = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where(function ($q) {
                $q->whereNull('orders.is_cancelled')
                    ->orWhere('orders.is_cancelled', '!=', 1);
            })
            ->get([
                'orders.id',
                'orders.id as order_id',
                'orders.billing_name',
                'orders.invoice_id',
                'orders.check_in_date',
                'orders.check_out_date',
                'orders.rooms',
                'orders.adults',
                'orders.childs',
                'orders.room_number_ids',
                'orders.payment_status',
                'orders.grand_total',
                'orders.other_details',
                'orders.checkin_checkout_status'
            ]);
        
        $bookings = [];
        foreach ($orders as $order) {
            $details = [];
            if (!empty($order->other_details)) {
                $decoded = json_decode($order->other_details, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $details = $decoded;
                }
            }
            
            // Check if room is assigned - same logic as calendar page
            $roomNumberIds = trim($order->room_number_ids ?? '');
            $hasRoomAssigned = !empty($roomNumberIds) && $roomNumberIds !== '0' && $roomNumberIds !== '';
            
            $bookings[] = [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'billing_name' => $order->billing_name,
                'invoice_id' => $order->invoice_id,
                'check_in_date' => $order->check_in_date,
                'check_out_date' => $order->check_out_date,
                'check_in_time' => $details['check_in_time'] ?? '14:00',
                'check_out_time' => $details['check_out_time'] ?? '12:00',
                'rooms' => $order->rooms,
                'adults' => $order->adults,
                'childs' => $order->childs,
                'room_number_ids' => $roomNumberIds,
                'payment_status' => $order->payment_status,
                'grand_total' => $order->grand_total,
                'reservation_type' => $details['reservation_type'] ?? 'confirmed',
                'has_room_assigned' => $hasRoomAssigned,
                'checkin_checkout_status' => $order->checkin_checkout_status
            ];
        }
        
        return $bookings;
    }

    private function getBookingPaymentData($orderId)
    {
        $order = self::$OrdersModel->find($orderId);
        if (!$order) {
            return ['grand_total' => 0, 'paid' => 0, 'balance' => 0];
        }
        
        $grandTotal = floatval($order->grand_total ?? 0);
        
        // Get total payments
        $payments = self::$OrderPaymentsModel
            ->where('order_id', $orderId)
            ->where('type', 'Payment')
            ->sum('amount');
        
        $paid = floatval($payments ?? 0);
        $balance = $grandTotal - $paid;
        
        return [
            'grand_total' => $grandTotal,
            'paid' => $paid,
            'balance' => $balance
        ];
    }

    private function calculateNights($checkIn, $checkOut)
    {
        if (!$checkOut) {
            return 1;
        }
        
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        
        return max(1, $checkInDate->diffInDays($checkOutDate));
    }

    private function buildQuickReservationRooms($vendorId){
        $roomsQuery = DB::table('rooms')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('rooms.status', 1)
            ->select([
                'rooms.id',
                'rooms.title',
                'rooms.hotel_id',
                'rooms.no_of_rooms',
                'rooms.extra_mattress_price',
                'hotel.title as hotel_title'
            ])
            ->orderBy('rooms.title', 'ASC');

        $rooms = $roomsQuery->get();
        if($rooms->isEmpty()){
            return [];
        }

        $roomIds = $rooms->pluck('id')->all();
        $roomPrices = self::$RoomPricesModel
            ->where('status', 1)
            ->whereIn('room_id', $roomIds)
            ->orderBy('price', 'ASC')
            ->get([
                'id',
                'room_id',
                'hotel_id',
                'price',
                'no_of_rooms',
                'no_of_guest',
                'no_of_child'
            ])
            ->groupBy('room_id');

        $payload = [];
        foreach($rooms as $room){
            $rates = [];
            if(isset($roomPrices[$room->id]) && $roomPrices[$room->id]->count() > 0){
                foreach($roomPrices[$room->id] as $priceRow){
                    $rates[] = [
                        'id' => (int)$priceRow->id,
                        'price' => (float)$priceRow->price,
                        'label' => 'Rs. '.number_format((float)$priceRow->price, 2).' / night',
                        'no_of_rooms' => (int)($priceRow->no_of_rooms ?? 0),
                        'no_of_guest' => (int)($priceRow->no_of_guest ?? 0),
                        'no_of_child' => (int)($priceRow->no_of_child ?? 0),
                    ];
                }
            }

            $payload[] = [
                'id' => (int)$room->id,
                'title' => $room->title,
                'hotel_id' => (int)$room->hotel_id,
                'hotel_title' => $room->hotel_title,
                'inventory' => (int)($room->no_of_rooms ?? 0),
                'extra_mattress_price' => (float)($room->extra_mattress_price ?? 0),
                'rates' => $rates
            ];
        }

        return $payload;
    }
}

