<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\RoomNumbers;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class AccountController extends Controller
{
    private static $UserModel;
    private static $HotelsModel;
    private static $RoomsModel;
    private static $RoomNumbersModel;
    private static $OrdersModel;

    public function __construct()
    {
        self::$UserModel = new User();
        self::$HotelsModel = new Hotels();
        self::$RoomsModel = new Rooms();
        self::$RoomNumbersModel = new RoomNumbers();
        self::$OrdersModel = new Orders();
    }

    public function index(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('vendor_id')) {
            return redirect('/');
        }

        $vendorId = $request->session()->get('vendor_id');
        $vendorProfile = $request->session()->get('vendor_profile');

        // Get account/user information
        $accountData = self::$UserModel->where('id', $vendorId)->first();

        // Get all properties/hotels for this vendor
        $properties = self::$HotelsModel
            ->where('vendor_id', $vendorId)
            ->where('status', 1)
            ->get();

        // Calculate statistics
        $totalProperties = $properties->count();
        $totalRooms = 0;
        $propertiesWithStats = [];

        foreach ($properties as $property) {
            // Get total rooms for this property
            $rooms = self::$RoomsModel
                ->where('hotel_id', $property->id)
                ->where('status', 1)
                ->get();

            $roomCount = 0;
            $occupiedRooms = 0;
            $occupancyRate = 0;

            foreach ($rooms as $room) {
                // Count room numbers for this room type
                $roomNumbers = self::$RoomNumbersModel
                    ->where('room_id', $room->id)
                    ->where('status', 1)
                    ->count();

                $roomCount += $roomNumbers;

                // Check occupied rooms (rooms with today's bookings)
                $today = date('Y-m-d');
                // Get room number IDs for this room
                $roomNumberIds = self::$RoomNumbersModel
                    ->where('room_id', $room->id)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();

                if (!empty($roomNumberIds)) {
                    // Count bookings where check_in_date is today OR today is between check_in_date and check_out_date
                    $occupied = DB::table('orders')
                        ->where('hotel_id', $property->id)
                        ->where('is_cancelled', '!=', 1)
                        ->where(function($query) use ($today, $roomNumberIds) {
                            $query->where(function($q) use ($today) {
                                // Today is check-in date OR today falls between check-in and check-out
                                $q->where('orders.check_in_date', '<=', $today)
                                  ->where('orders.check_out_date', '>=', $today);
                            })
                            ->where(function($q) use ($roomNumberIds) {
                                // Check if room_number_ids contains any of our room numbers
                                foreach ($roomNumberIds as $roomNumberId) {
                                    $q->orWhere('room_number_ids', 'like', '%"' . $roomNumberId . '"%')
                                      ->orWhere('room_number_ids', 'like', '%' . $roomNumberId . '%');
                                }
                            });
                        })
                        ->count();

                    $occupiedRooms += $occupied;
                }
            }

            $totalRooms += $roomCount;

            // Calculate occupancy rate
            if ($roomCount > 0) {
                $occupancyRate = round(($occupiedRooms / $roomCount) * 100);
            }

            $propertiesWithStats[] = [
                'id' => $property->id,
                'title' => $property->title,
                'address' => $property->address,
                'city' => $property->city,
                'state' => $property->state,
                'status' => $property->status == 1 ? 'active' : 'inactive',
                'total_rooms' => $roomCount,
                'occupied_rooms' => $occupiedRooms,
                'occupancy_rate' => $occupancyRate,
            ];
        }

        // Prepare account information
        $accountInfo = [
            'account_name' => $accountData->company ?? $accountData->name ?? 'N/A',
            'account_status' => $accountData->status == 1 ? 'Active' : 'Inactive',
            'location' => ($accountData->city ?? '') . ($accountData->state ? ', ' . $accountData->state : '') . ($accountData->country ? ', ' . $accountData->country : ''),
            'contact' => $accountData->mobile ?? 'N/A',
            'email' => $accountData->email ?? 'N/A',
            'user_id' => $accountData->login_id ?? 'N/A',
            'property_code' => $accountData->property_id ?? 'N/A',
            'total_properties' => $totalProperties,
            'total_rooms' => $totalRooms,
        ];

        return view('account.index', [
            'accountInfo' => $accountInfo,
            'properties' => $propertiesWithStats,
            'vendorProfile' => $vendorProfile,
        ]);
    }
}

