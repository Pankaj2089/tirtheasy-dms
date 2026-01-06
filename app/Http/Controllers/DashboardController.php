<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\RoomNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private static $OrdersModel;
    private static $HotelsModel;
    private static $RoomsModel;
    private static $RoomNumbersModel;

    public function __construct()
    {
        self::$OrdersModel = new Orders();
        self::$HotelsModel = new Hotels();
        self::$RoomsModel = new Rooms();
        self::$RoomNumbersModel = new RoomNumbers();
    }

    public function index(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('vendor_login_id')) {
            return redirect('/');
        }

        $vendorId = $request->session()->get('vendor_id');
        $vendorProfile = $request->session()->get('vendor_profile');

        // Get default data for today
        $filter = $request->get('filter', 'today');
        $date = $request->get('date', date('Y-m-d'));

        $dashboardData = $this->getDashboardData($vendorId, $filter, $date);

        return view('dashboard.index', [
            'dashboardData' => $dashboardData,
            'vendorProfile' => $vendorProfile,
            'currentFilter' => $filter,
            'currentDate' => $date,
        ]);
    }

    public function getData(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $filter = $request->get('filter', 'today');
        $date = $request->get('date', date('Y-m-d'));

        $dashboardData = $this->getDashboardData($vendorId, $filter, $date);

        return response()->json(['success' => true, 'data' => $dashboardData]);
    }

    private function getDashboardData($vendorId, $filter, $date)
    {
        $dateObj = Carbon::parse($date);
        
        // Determine date range based on filter
        switch ($filter) {
            case 'today':
                $startDate = $dateObj->copy()->startOfDay();
                $endDate = $dateObj->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $dateObj->copy()->startOfWeek();
                $endDate = $dateObj->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $dateObj->copy()->startOfMonth();
                $endDate = $dateObj->copy()->endOfMonth();
                break;
            case 'year':
                $startDate = $dateObj->copy()->startOfYear();
                $endDate = $dateObj->copy()->endOfYear();
                break;
            default:
                $startDate = $dateObj->copy()->startOfDay();
                $endDate = $dateObj->copy()->endOfDay();
        }

        // Get arrivals (check-in today/period)
        $arrivals = DB::table('orders')
            ->where('vendor_id', $vendorId)
            ->where('is_cancelled', 0)
            ->whereBetween('check_in_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $arrivalPending = $arrivals->where('payment_status', '!=', 'completed')->count();
        $arrivalCompleted = $arrivals->where('payment_status', 'completed')->count();
        $arrivalTotal = $arrivals->count();
        $arrivalPercentage = $arrivalTotal > 0 ? round(($arrivalCompleted / $arrivalTotal) * 100) : 0;

        // Get departures (check-out today/period)
        $departures = DB::table('orders')
            ->where('vendor_id', $vendorId)
            ->where('is_cancelled', 0)
            ->whereBetween('check_out_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $departurePending = $departures->where('payment_status', '!=', 'completed')->count();
        $departureCheckedOut = $departures->where('payment_status', 'completed')->count();
        $departureTotal = $departures->count();
        $departurePercentage = $departureTotal > 0 ? round(($departureCheckedOut / $departureTotal) * 100) : 0;

        // Get in-house guests (checked in but not checked out)
        $today = Carbon::today()->format('Y-m-d');
        $inHouse = DB::table('orders')
            ->where('vendor_id', $vendorId)
            ->where('is_cancelled', 0)
            ->where('check_in_date', '<=', $today)
            ->where('check_out_date', '>', $today)
            ->get();

        $inHouseAdults = $inHouse->sum('adults');
        $inHouseChildren = $inHouse->sum('childs');
        $inHouseTotal = $inHouseAdults + $inHouseChildren;
        $inHousePercentage = $inHouseTotal > 0 ? round(($inHouseAdults / $inHouseTotal) * 100) : 0;

        // Get room status
        $totalRooms = DB::table('room_numbers')
            ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('room_numbers.status', 1)
            ->where('rooms.status', 1)
            ->count();

        // Count occupied rooms (simplified - count bookings with room assignments)
        $occupiedRooms = DB::table('orders')
            ->where('vendor_id', $vendorId)
            ->where('is_cancelled', 0)
            ->where('check_in_date', '<=', $today)
            ->where('check_out_date', '>', $today)
            ->whereNotNull('room_number_ids')
            ->where('room_number_ids', '!=', '')
            ->count();

        $vacantRooms = max(0, $totalRooms - $occupiedRooms);
        $roomOccupancyPercentage = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Get housekeeping status (simplified - can be enhanced based on actual housekeeping table)
        // Clean rooms are those not occupied and not blocked
        // maintenance_blocks uses room_number_id, so we need to join through room_numbers -> rooms -> hotel
        $blockedRoomNumberIds = DB::table('maintenance_blocks')
            ->join('room_numbers', 'maintenance_blocks.room_number_id', '=', 'room_numbers.id')
            ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('maintenance_blocks.start_date', '<=', $today)
            ->where('maintenance_blocks.end_date', '>=', $today)
            ->pluck('maintenance_blocks.room_number_id')
            ->toArray();

        // Count blocked room numbers (not rooms, since maintenance is on room numbers)
        $blockedRoomNumbersCount = count($blockedRoomNumberIds);

        // Clean = Total - Occupied - Blocked
        $housekeepingClean = max(0, $totalRooms - $occupiedRooms - $blockedRoomNumbersCount);

        $housekeepingDirty = $occupiedRooms;
        $housekeepingAssign = 0; // Can be enhanced with actual housekeeping assignment table
        $housekeepingBlock = DB::table('maintenance_blocks')
            ->join('room_numbers', 'maintenance_blocks.room_number_id', '=', 'room_numbers.id')
            ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('maintenance_blocks.start_date', '<=', $today)
            ->where('maintenance_blocks.end_date', '>=', $today)
            ->count();

        // Get activity feeds
        $activityFeeds = DB::table('orders')
            ->where('vendor_id', $vendorId)
            ->where('is_cancelled', 0)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('check_out_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($order) {
                $type = 'Check In';
                $class = 'btn-primary';
                if (Carbon::parse($order->check_out_date)->isToday()) {
                    $type = 'Check Out';
                    $class = 'btn-danger';
                } elseif ($order->is_cancelled) {
                    $type = 'Cancelled';
                    $class = 'btn-warning';
                }
                return [
                    'message' => $order->billing_name . ' - ' . $order->billing_phone,
                    'type' => $type,
                    'class' => $class,
                    'date' => $order->created_at
                ];
            });

        // Get revenue metrics
        $revenueData = $this->getRevenueMetrics($vendorId, $startDate, $endDate);

        return [
            'arrivals' => [
                'total' => $arrivalTotal,
                'completed' => $arrivalCompleted,
                'pending' => $arrivalPending,
                'percentage' => $arrivalPercentage
            ],
            'departures' => [
                'total' => $departureTotal,
                'checkedOut' => $departureCheckedOut,
                'pending' => $departurePending,
                'percentage' => $departurePercentage
            ],
            'inHouse' => [
                'adults' => $inHouseAdults,
                'children' => $inHouseChildren,
                'total' => $inHouseTotal,
                'percentage' => $inHousePercentage
            ],
            'roomStatus' => [
                'total' => $totalRooms,
                'occupied' => $occupiedRooms,
                'vacant' => $vacantRooms,
                'complimentary' => 0,
                'percentage' => $roomOccupancyPercentage
            ],
            'housekeeping' => [
                'clean' => $housekeepingClean,
                'assign' => $housekeepingAssign,
                'dirty' => $housekeepingDirty,
                'block' => $housekeepingBlock
            ],
            'activityFeeds' => $activityFeeds,
            'revenue' => $revenueData
        ];
    }

    private function getRevenueMetrics($vendorId, $startDate, $endDate)
    {
        $orders = DB::table('orders')
            ->where('vendor_id', $vendorId)
            ->where('is_cancelled', 0)
            ->whereBetween('check_in_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $totalRevenue = $orders->sum('grand_total');
        $totalNights = $orders->sum('number_of_nights');
        $totalRooms = $orders->sum('rooms');
        
        // Get total available rooms for period
        $totalAvailableRooms = DB::table('room_numbers')
            ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('room_numbers.status', 1)
            ->where('rooms.status', 1)
            ->count();

        $daysInPeriod = $startDate->diffInDays($endDate) + 1;
        $totalRoomNights = $totalAvailableRooms * $daysInPeriod;

        $occupancy = $totalRoomNights > 0 ? round(($totalNights / $totalRoomNights) * 100, 2) : 0;
        $adr = $totalNights > 0 ? round($totalRevenue / $totalNights, 2) : 0;
        $revpar = $totalRoomNights > 0 ? round($totalRevenue / $totalRoomNights, 2) : 0;
        $revpac = $totalNights > 0 ? round($totalRevenue / $totalNights, 2) : 0;
        $alos = $orders->count() > 0 ? round($totalNights / $orders->count(), 2) : 0;
        
        // Average lead time (simplified - difference between booking date and check-in)
        $avgLeadTime = 0;
        if ($orders->count() > 0) {
            $totalLeadTime = 0;
            foreach ($orders as $order) {
                $bookingDate = Carbon::parse($order->created_at);
                $checkInDate = Carbon::parse($order->check_in_date);
                $totalLeadTime += $bookingDate->diffInDays($checkInDate);
            }
            $avgLeadTime = round($totalLeadTime / $orders->count(), 2);
        }

        return [
            'occupancy' => $occupancy,
            'pickup' => $orders->count(),
            'cancellations' => DB::table('orders')
                ->where('vendor_id', $vendorId)
                ->where('is_cancelled', 1)
                ->whereBetween('check_in_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->count(),
            'alos' => $alos,
            'avgLeadTime' => $avgLeadTime,
            'adr' => $adr,
            'revenue' => $totalRevenue,
            'revpar' => $revpar,
            'revpac' => $revpac
        ];
    }
}

