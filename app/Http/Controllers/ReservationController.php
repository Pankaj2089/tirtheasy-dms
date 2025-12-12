<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\Orders;
use App\Models\OrderPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
    private static $HotelsModel;
    private static $OrdersModel;
    private static $OrderPaymentsModel;

    public function __construct()
    {
        self::$HotelsModel = new Hotels();
        self::$OrdersModel = new Orders();
        self::$OrderPaymentsModel = new OrderPayments();
    }

    public function index(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return redirect('/');
        }

        $vendorId = $request->session()->get('vendor_id');
        $hotels = $this->getVendorHotels($vendorId);
        
        // Get filter date from request, default to today
        $filterDateInput = $request->query('filter_date');
        $filterDate = $filterDateInput ? Carbon::parse($filterDateInput)->startOfDay() : Carbon::today();
        
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
            $checkInDate = Carbon::parse($booking['check_in_date']);
            $checkOutDate = $booking['check_out_date'] ? Carbon::parse($booking['check_out_date']) : null;
            
            // Parse check-in time
            $checkInTime = $booking['check_in_time'] ?? '14:00';
            $checkInDateTime = Carbon::parse($booking['check_in_date'] . ' ' . $checkInTime);
            
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
            
            // Check if guest has checked in (has room assigned)
            $hasCheckedIn = $booking['has_room_assigned'] ?? false;
            
            // Filter by selected date if provided
            $useFilterDate = $filterDateInput !== null;
            
            // Arrivals: Show all bookings that haven't checked in yet
            // - Today's bookings where check-in time has passed
            // - Future bookings (upcoming) - all future check-in dates
            if ($useFilterDate) {
                // Filter by selected date - check-in date must match filter date
                if ($checkInDate->format('Y-m-d') === $filterDate->format('Y-m-d') && !$hasCheckedIn) {
                    // If filter date is today or past, check if check-in time has passed
                    if ($filterDate->isToday() || $filterDate->isPast()) {
                        if ($checkInDateTime->isPast()) {
                            $arrivals[] = $bookingData;
                        }
                    } else {
                        // Filter date is in the future, show all upcoming bookings for that date
                        $arrivals[] = $bookingData;
                    }
                }
            } else {
                // Default: Show all bookings that haven't checked in yet
                // - Today's bookings where check-in time has passed
                // - All future bookings (upcoming)
                if (!$hasCheckedIn) {
                    if ($checkInDate->isToday() && $checkInDateTime->isPast()) {
                        // Today's booking where time has passed
                        $arrivals[] = $bookingData;
                    } elseif ($checkInDate->isFuture()) {
                        // All upcoming bookings (future check-in dates)
                        $arrivals[] = $bookingData;
                    }
                }
            }
            
            // Departures: Check-out date matches filter date (or today if no filter)
            if ($useFilterDate) {
                if ($checkOutDate && $checkOutDate->format('Y-m-d') === $filterDate->format('Y-m-d')) {
                    $departures[] = $bookingData;
                }
            } else {
                if ($checkOutDate && $checkOutDate->isToday()) {
                    $departures[] = $bookingData;
                }
            }
            
            // In-House: Guests who have already checked in (room assigned) AND booking is ongoing
            // Must have room assigned (hasCheckedIn = true)
            // Check-in date must be today or in the past (already checked in)
            // Check-out date must be today or in the future (not checked out yet)
            if ($hasCheckedIn) {
                if ($useFilterDate) {
                    // Filter by selected date - include if check-in <= filter date AND (no check-out OR check-out >= filter date)
                    if ($checkInDate->format('Y-m-d') <= $filterDate->format('Y-m-d')) {
                        if (!$checkOutDate || $checkOutDate->format('Y-m-d') >= $filterDate->format('Y-m-d')) {
                            $inHouse[] = $bookingData;
                        }
                    }
                } else {
                    // Default: Only include if check-in is today or in the past (already checked in)
                    // AND check-out is today or in the future (not checked out yet)
                    if ($checkInDate->isToday() || $checkInDate->isPast()) {
                        if (!$checkOutDate) {
                            // No check-out date, they're in-house
                            $inHouse[] = $bookingData;
                        } elseif ($checkOutDate->isToday() || $checkOutDate->isFuture()) {
                            // Check-out is today or future, they're in-house
                            $inHouse[] = $bookingData;
                        }
                    }
                    // If check-in is in the future, don't add to in-house (they haven't checked in yet)
                }
            }
            
            // Reservations: Only future bookings (check-in date is in the future)
            // OR bookings that haven't arrived yet (check-in is today but check-in time hasn't come yet)
            if ($checkInDate->isFuture()) {
                $reservations[] = $bookingData;
            } elseif ($checkInDate->isToday() && $checkInDateTime->isFuture()) {
                // Check-in is today but check-in time hasn't arrived yet
                $reservations[] = $bookingData;
            }
        }
        
        $siteUrl = env('APP_URL');
        $selectedFilterDate = $filterDateInput ? $filterDate->format('Y-m-d') : date('Y-m-d');
        
        return view('reservation.index', compact('reservations', 'arrivals', 'departures', 'inHouse', 'hotels', 'siteUrl', 'selectedFilterDate'));
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
                'orders.other_details'
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
                'has_room_assigned' => $hasRoomAssigned
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
}

