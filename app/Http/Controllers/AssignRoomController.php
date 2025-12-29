<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\RoomNumbers;
use App\Models\MaintenanceBlocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Validator;
use Session;

class AssignRoomController extends Controller
{
    private static $OrdersModel;
    private static $RoomNumbersModel;
    private static $MaintenanceBlocksModel;

    public function __construct()
    {
        self::$OrdersModel = new Orders();
        self::$RoomNumbersModel = new RoomNumbers();
        self::$MaintenanceBlocksModel = new MaintenanceBlocks();
    }

    /**
     * Fetch unassigned bookings for a specific date
     */
    public function fetchUnassignedBookings(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $hotelId = $request->query('hotel_id');

        try {
            $targetDate = $request->query('date', Carbon::today()->toDateString());
            $targetDate = Carbon::parse($targetDate)->toDateString();
        } catch (\Throwable $throwable) {
            return response()->json(['success' => false, 'message' => 'Invalid date supplied.'], 422);
        }

        $query = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->join('rooms', 'orders.room_id', '=', 'rooms.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where(function ($q) {
                $q->whereNull('orders.room_number_ids')
                    ->orWhere('orders.room_number_ids', '')
                    ->orWhere('orders.room_number_ids', '0')
                    ->orWhereRaw("TRIM(COALESCE(orders.room_number_ids, '')) = ''");
            })
            // IMPORTANT: Show bookings ONLY where check-in date exactly matches target date
            // Do NOT filter by checkout date - only show bookings with check-in date = selected date
            ->whereDate('orders.check_in_date', '=', $targetDate)
            ->where(function ($q) {
                $q->whereNull('orders.is_cancelled')
                    ->orWhere('orders.is_cancelled', '!=', 1);
            });

        if ($hotelId && is_numeric($hotelId)) {
            $query->where('hotel.id', (int)$hotelId);
        }

        $records = $query
            ->orderBy('orders.check_in_date', 'ASC')
            ->get([
                'orders.id',
                'orders.invoice_id',
                'orders.billing_name',
                'orders.rooms',
                'orders.adults',
                'orders.childs',
                'orders.check_in_date',
                'orders.check_out_date',
                'orders.hotel_id',
                'orders.room_id',
                'rooms.title as room_title',
                'hotel.title as hotel_name'
            ]);

        $bookings = $records->map(function ($booking) {
            $checkOut = $booking->check_out_date ?: $booking->check_in_date;
            return [
                'id' => (int)$booking->id,
                'code' => 'ORD-' . $booking->id,
                'invoice_id' => $booking->invoice_id,
                'guest_name' => $booking->billing_name ?? 'Guest',
                'rooms' => (int)($booking->rooms ?? 0),
                'adults' => (int)($booking->adults ?? 0),
                'children' => (int)($booking->childs ?? 0),
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $checkOut,
                'hotel_id' => (int)$booking->hotel_id,
                'room_id' => (int)$booking->room_id,
                'room_type' => $booking->room_title,
                'hotel_name' => $booking->hotel_name,
                'stay_label' => Carbon::parse($booking->check_in_date)->format('d M, Y') . ' to ' . Carbon::parse($checkOut)->format('d M, Y'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'date' => $targetDate,
            'bookings' => $bookings
        ]);
    }

    /**
     * Get available room numbers for a booking on a specific date
     */
    public function availableRoomNumbers(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $bookingId = $request->query('booking_id');
        $dateInput = $request->query('date');

        if (!$bookingId || !is_numeric($bookingId)) {
            return response()->json(['success' => false, 'message' => 'Invalid booking supplied.'], 422);
        }

        $booking = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->join('rooms', 'orders.room_id', '=', 'rooms.id')
            ->where('orders.id', (int)$bookingId)
            ->where('hotel.vendor_id', $vendorId)
            ->select('orders.*', 'rooms.title as room_title', 'hotel.title as hotel_name')
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        try {
            $targetDate = $dateInput ? Carbon::parse($dateInput)->toDateString() : Carbon::parse($booking->check_in_date)->toDateString();
        } catch (\Throwable $throwable) {
            return response()->json(['success' => false, 'message' => 'Invalid date supplied.'], 422);
        }

        $roomNumbers = self::$RoomNumbersModel
            ->where('status', 1)
            ->where('room_id', $booking->room_id)
            ->where('hotel_id', $booking->hotel_id)
            ->get(['id', 'full_title', 'title', 'prefix']);

        if ($roomNumbers->isEmpty()) {
            return response()->json([
                'success' => true,
                'rooms' => []
            ]);
        }

        // Collect room numbers that are already booked for the target date
        $unavailableRoomIds = [];

        $overlappingOrders = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('orders.id', '!=', $booking->id)
            ->whereNotNull('orders.room_number_ids')
            ->where(function ($query) use ($targetDate) {
                $query->where(function ($q) use ($targetDate) {
                    $q->where('check_in_date', '<=', $targetDate)
                        ->where(function ($inner) use ($targetDate) {
                            $inner->where('check_out_date', '>=', $targetDate)
                                ->orWhereNull('check_out_date');
                        });
                });
            })
            ->get(['orders.room_number_ids']);

        foreach ($overlappingOrders as $order) {
            $ids = array_filter(array_map(function ($id) {
                return (int)trim($id);
            }, explode(',', $order->room_number_ids ?? '')));
            foreach ($ids as $id) {
                $unavailableRoomIds[$id] = true;
            }
        }

        // Include maintenance blocks
        $maintenanceBlocks = self::$MaintenanceBlocksModel
            ->where('vendor_id', $vendorId)
            ->where('start_date', '<=', $targetDate)
            ->where('end_date', '>=', $targetDate)
            ->get(['room_number_id']);

        foreach ($maintenanceBlocks as $block) {
            $unavailableRoomIds[$block->room_number_id] = true;
        }

        $available = $roomNumbers->filter(function ($room) use ($unavailableRoomIds) {
            return !isset($unavailableRoomIds[$room->id]);
        })->values()->map(function ($room) {
            return [
                'id' => (int)$room->id,
                'label' => $room->full_title ?? $room->title ?? 'Room'
            ];
        });

        return response()->json([
            'success' => true,
            'rooms' => $available
        ]);
    }

    /**
     * Assign room number to a booking
     */
    public function assignRoomToBooking(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'booking_id' => ['required', 'integer', 'min:1'],
            'room_number_ids' => ['required', 'string'],
        ], [
            'booking_id.required' => 'Booking ID is required.',
            'room_number_ids.required' => 'Room number(s) is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $vendorId = $request->session()->get('vendor_id');
        $bookingId = (int)$request->input('booking_id');
        $roomNumberIdsInput = $request->input('room_number_ids');
        
        // Parse comma-separated room IDs
        $roomNumberIds = array_filter(array_map(function ($id) {
            return (int)trim($id);
        }, explode(',', $roomNumberIdsInput ?? '')));
        
        if (empty($roomNumberIds)) {
            return response()->json(['success' => false, 'message' => 'At least one room number is required.'], 422);
        }

        // Verify booking belongs to vendor
        $booking = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->where('orders.id', $bookingId)
            ->where('hotel.vendor_id', $vendorId)
            ->select('orders.*')
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        // Verify all room numbers exist and belong to the booking's room
        $roomNumbers = self::$RoomNumbersModel
            ->whereIn('id', $roomNumberIds)
            ->where('room_id', $booking->room_id)
            ->where('hotel_id', $booking->hotel_id)
            ->where('status', 1)
            ->get();

        if ($roomNumbers->count() !== count($roomNumberIds)) {
            return response()->json(['success' => false, 'message' => 'One or more room numbers not found or invalid.'], 404);
        }

        // Check if room is available for the booking dates
        $checkInDate = $booking->check_in_date;
        $checkOutDate = $booking->check_out_date ?: $booking->check_in_date;

        // Check for overlapping bookings
        $overlappingOrders = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('orders.id', '!=', $booking->id)
            ->whereNotNull('room_number_ids')
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where(function ($q) use ($checkInDate, $checkOutDate) {
                    $q->where('check_in_date', '<=', $checkInDate)
                        ->where(function ($inner) use ($checkInDate) {
                            $inner->where('check_out_date', '>', $checkInDate)
                                ->orWhereNull('check_out_date');
                        });
                })
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('check_in_date', '<', $checkOutDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                    })
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('check_in_date', '>=', $checkInDate)
                            ->where('check_out_date', '<=', $checkOutDate);
                    })
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('check_in_date', '<=', $checkInDate)
                            ->where('check_out_date', '>=', $checkOutDate);
                    });
            })
            ->get(['orders.room_number_ids']);

        // Check for conflicts with all selected room IDs
        foreach ($overlappingOrders as $order) {
            $ids = array_filter(array_map(function ($id) {
                return (int)trim($id);
            }, explode(',', $order->room_number_ids ?? '')));
            
            foreach ($roomNumberIds as $roomId) {
                if (in_array($roomId, $ids)) {
                    $roomLabel = $roomNumbers->firstWhere('id', $roomId)->full_title ?? $roomNumbers->firstWhere('id', $roomId)->title ?? "Room {$roomId}";
                    return response()->json([
                        'success' => false,
                        'message' => "Room {$roomLabel} is already booked for the selected dates."
                    ], 422);
                }
            }
        }

        // Check maintenance blocks for all selected rooms
        $maintenanceBlocks = self::$MaintenanceBlocksModel
            ->where('vendor_id', $vendorId)
            ->whereIn('room_number_id', $roomNumberIds)
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where(function ($q) use ($checkInDate, $checkOutDate) {
                    $q->where('start_date', '<=', $checkInDate)
                        ->where('end_date', '>', $checkInDate);
                })
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('start_date', '<', $checkOutDate)
                            ->where('end_date', '>=', $checkOutDate);
                    })
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('start_date', '>=', $checkInDate)
                            ->where('end_date', '<=', $checkOutDate);
                    })
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('start_date', '<=', $checkInDate)
                            ->where('end_date', '>=', $checkOutDate);
                    });
            })
            ->exists();

        if ($maintenanceBlocks) {
            $blockedRooms = self::$MaintenanceBlocksModel
                ->where('vendor_id', $vendorId)
                ->whereIn('room_number_id', $roomNumberIds)
                ->where(function ($query) use ($checkInDate, $checkOutDate) {
                    $query->where(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('start_date', '<=', $checkInDate)
                            ->where('end_date', '>', $checkInDate);
                    })
                        ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                            $q->where('start_date', '<', $checkOutDate)
                                ->where('end_date', '>=', $checkOutDate);
                        })
                        ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                            $q->where('start_date', '>=', $checkInDate)
                                ->where('end_date', '<=', $checkOutDate);
                        })
                        ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                            $q->where('start_date', '<=', $checkInDate)
                                ->where('end_date', '>=', $checkOutDate);
                        });
                })
                ->pluck('room_number_id')
                ->unique();
            
            $blockedRoomLabels = $roomNumbers->whereIn('id', $blockedRooms)->pluck('full_title')->filter()->values()->all();
            $blockedText = !empty($blockedRoomLabels) ? implode(', ', $blockedRoomLabels) : 'one or more rooms';
            
            return response()->json([
                'success' => false,
                'message' => "Room(s) {$blockedText} is/are under maintenance for the selected dates."
            ], 422);
        }

        // Assign the rooms (comma-separated)
        try {
            self::$OrdersModel->UpdateRecord([
                'id' => $bookingId,
                'room_number_ids' => implode(',', $roomNumberIds)
            ]);

            $roomLabels = $roomNumbers->pluck('full_title')->filter()->values()->all();
            $roomText = !empty($roomLabels) ? implode(', ', $roomLabels) : implode(', ', $roomNumberIds);

            return response()->json([
                'success' => true,
                'message' => 'Room(s) assigned successfully.',
                'room_number' => $roomText
            ]);
        } catch (\Throwable $throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to assign room. Please try again.',
                'error' => $throwable->getMessage()
            ], 500);
        }
    }
}

