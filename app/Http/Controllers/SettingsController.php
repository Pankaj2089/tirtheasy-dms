<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\PriceSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class SettingsController extends Controller
{
    private static $UserModel;
    private static $HotelsModel;
    private static $RoomsModel;
    private static $PriceSettingsModel;

    public function __construct()
    {
        self::$UserModel = new User();
        self::$HotelsModel = new Hotels();
        self::$RoomsModel = new Rooms();
        self::$PriceSettingsModel = new PriceSettings();
    }

    private function getVendorHotels($vendorId){
        // Same as calendar - only show hotels that have rooms with room numbers
        return DB::table('hotel')
            ->join('rooms', 'hotel.id', '=', 'rooms.hotel_id')
            ->join('room_numbers', 'rooms.id', '=', 'room_numbers.room_id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('hotel.status', 1)
            ->where('rooms.status', 1)
            ->where('room_numbers.status', 1)
            ->select('hotel.id', 'hotel.title')
            ->distinct()
            ->orderBy('hotel.title', 'ASC')
            ->get();
    }

    private function getVendorRoomCategories($vendorId){
        // Same as calendar - only show room types that have at least one available room number
        return DB::table('rooms')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->join('room_numbers', 'rooms.id', '=', 'room_numbers.room_id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('rooms.status', 1)
            ->where('room_numbers.status', 1)
            ->select('rooms.id', 'rooms.title', 'rooms.hotel_id')
            ->distinct()
            ->orderBy('rooms.title', 'ASC')
            ->get();
    }

    public function index(Request $request)
    {
        // Check if user is logged in (same as calendar)
        if (!$request->session()->has('vendor_login_id')) {
            return redirect('/');
        }

        $vendorId = $request->session()->get('vendor_id');
        $vendorProfile = $request->session()->get('vendor_profile');

        // Get hotels and room types using same methods as calendar
        $hotels = $this->getVendorHotels($vendorId);
        $roomTypes = $this->getVendorRoomCategories($vendorId);

        // Get existing price settings for this vendor
        $priceSettings = self::$PriceSettingsModel
            ->where('vendor_id', $vendorId)
            ->get()
            ->keyBy(function($item) {
                return $item->hotel_id . '_' . $item->room_id;
            });

        return view('settings.index', [
            'hotels' => $hotels,
            'roomTypes' => $roomTypes,
            'priceSettings' => $priceSettings,
            'vendorProfile' => $vendorProfile,
        ]);
    }

    public function store(Request $request)
    {
        // Check if user is logged in (same as calendar)
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');

        $request->validate([
            'hotel_id' => 'required|integer',
            'room_id' => 'required|integer',
            'advance_booking_days' => 'nullable|integer',
            'apply_dynamic_price' => 'nullable|boolean',
            'common_price' => 'nullable|numeric',
            'gst_percentage' => 'nullable|numeric',
            'january' => 'nullable|numeric',
            'february' => 'nullable|numeric',
            'march' => 'nullable|numeric',
            'april' => 'nullable|numeric',
            'may' => 'nullable|numeric',
            'june' => 'nullable|numeric',
            'july' => 'nullable|numeric',
            'august' => 'nullable|numeric',
            'september' => 'nullable|numeric',
            'october' => 'nullable|numeric',
            'november' => 'nullable|numeric',
            'december' => 'nullable|numeric',
        ]);

        try {
            // Check if setting exists
            $existing = self::$PriceSettingsModel
                ->where('vendor_id', $vendorId)
                ->where('hotel_id', $request->hotel_id)
                ->where('room_id', $request->room_id)
                ->first();

            $data = [
                'vendor_id' => $vendorId,
                'hotel_id' => $request->hotel_id,
                'room_id' => $request->room_id,
                'advance_booking_days' => $request->advance_booking_days ?? null,
                'apply_dynamic_price' => $request->apply_dynamic_price ?? 0,
                'common_price' => $request->common_price ?? null,
                'gst_percentage' => $request->gst_percentage ?? null,
                'january' => $request->january ?? null,
                'february' => $request->february ?? null,
                'march' => $request->march ?? null,
                'april' => $request->april ?? null,
                'may' => $request->may ?? null,
                'june' => $request->june ?? null,
                'july' => $request->july ?? null,
                'august' => $request->august ?? null,
                'september' => $request->september ?? null,
                'october' => $request->october ?? null,
                'november' => $request->november ?? null,
                'december' => $request->december ?? null,
            ];

            if ($existing) {
                // Update existing
                self::$PriceSettingsModel->where('id', $existing->id)->update($data);
                $message = 'Price settings updated successfully';
            } else {
                // Create new
                self::$PriceSettingsModel->create($data);
                $message = 'Price settings saved successfully';
            }

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error saving settings: ' . $e->getMessage()], 500);
        }
    }

    public function getSettings(Request $request)
    {
        // Check if user is logged in (same as calendar)
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $hotelId = $request->input('hotel_id');
        $roomId = $request->input('room_id');

        if (!$hotelId || !$roomId) {
            return response()->json(['success' => false, 'message' => 'Hotel ID and Room ID are required']);
        }

        $settings = self::$PriceSettingsModel
            ->where('vendor_id', $vendorId)
            ->where('hotel_id', $hotelId)
            ->where('room_id', $roomId)
            ->first();

        if ($settings) {
            return response()->json(['success' => true, 'data' => $settings]);
        }

        return response()->json(['success' => true, 'data' => null]);
    }

    public function getRoomTypes(Request $request)
    {
        // Check if user is logged in (same as calendar)
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $hotelId = $request->input('hotel_id');

        if (!$hotelId) {
            return response()->json(['success' => false, 'message' => 'Hotel ID is required']);
        }

        // Get room types for the selected hotel (same as calendar)
        $roomTypes = DB::table('rooms')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->join('room_numbers', 'rooms.id', '=', 'room_numbers.room_id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('rooms.hotel_id', $hotelId)
            ->where('rooms.status', 1)
            ->where('room_numbers.status', 1)
            ->select('rooms.id', 'rooms.title', 'rooms.hotel_id')
            ->distinct()
            ->orderBy('rooms.title', 'ASC')
            ->get();

        return response()->json(['success' => true, 'data' => $roomTypes]);
    }
}

