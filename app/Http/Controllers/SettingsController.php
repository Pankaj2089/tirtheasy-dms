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
            ->select('hotel.id', 'hotel.title', 'hotel.ordering')
            ->distinct()
            ->orderBy('hotel.ordering', 'ASC')
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
            ->select('rooms.id', 'rooms.title', 'rooms.hotel_id', 'rooms.ordering')
            ->distinct()
            ->orderBy('rooms.ordering', 'ASC')
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
            ->select('rooms.id', 'rooms.title', 'rooms.hotel_id', 'rooms.ordering')
            ->distinct()
            ->orderBy('rooms.ordering', 'ASC')
            ->get();

        return response()->json(['success' => true, 'data' => $roomTypes]);
    }

    private function normalizePerPage(?int $perPage): int
    {
        $perPage = $perPage ?: 50;
        if ($perPage < 1) {
            $perPage = 1;
        }
        if ($perPage > 200) {
            $perPage = 200;
        }
        return $perPage;
    }

    public function orderingHotels(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $perPage = $this->normalizePerPage((int)$request->query('per_page'));

        $query = DB::table('hotel')
            ->join('rooms', 'hotel.id', '=', 'rooms.hotel_id')
            ->join('room_numbers', 'rooms.id', '=', 'room_numbers.room_id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('hotel.status', 1)
            ->where('rooms.status', 1)
            ->where('room_numbers.status', 1)
            ->select('hotel.id', 'hotel.title', 'hotel.ordering')
            ->distinct()
            ->orderBy('hotel.ordering', 'ASC');

        $result = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }

    public function orderingRooms(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $perPage = $this->normalizePerPage((int)$request->query('per_page'));

        $request->validate([
            'hotel_id' => 'required|integer',
        ]);

        $hotelId = (int)$request->query('hotel_id');

        $query = DB::table('rooms')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->join('room_numbers', 'rooms.id', '=', 'room_numbers.room_id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('rooms.hotel_id', $hotelId)
            ->where('hotel.status', 1)
            ->where('rooms.status', 1)
            ->where('room_numbers.status', 1)
            ->select('rooms.id', 'rooms.title', 'rooms.ordering', 'rooms.hotel_id')
            ->distinct()
            ->orderBy('rooms.ordering', 'ASC');

        $result = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }

    public function orderingRoomNumbers(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $perPage = $this->normalizePerPage((int)$request->query('per_page'));

        $request->validate([
            'hotel_id' => 'required|integer',
            'room_id' => 'required|integer',
        ]);

        $hotelId = (int)$request->query('hotel_id');
        $roomId = (int)$request->query('room_id');

        $query = DB::table('room_numbers')
            ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
            ->join('hotel', 'room_numbers.hotel_id', '=', 'hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('room_numbers.hotel_id', $hotelId)
            ->where('room_numbers.room_id', $roomId)
            ->where('hotel.status', 1)
            ->where('rooms.status', 1)
            ->where('room_numbers.status', 1)
            ->select('room_numbers.id', 'room_numbers.full_title as title', 'room_numbers.ordering', 'room_numbers.hotel_id', 'room_numbers.room_id')
            ->orderBy('room_numbers.ordering', 'ASC');

        $result = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }

    public function updateOrdering(Request $request)
    {
        if (!$request->session()->has('vendor_login_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');

        $request->validate([
            'type' => 'required|string|in:hotel,room,room_number',
            'ordered_ids' => 'required|array|min:1',
            'ordered_ids.*' => 'integer',
            'hotel_id' => 'nullable|integer',
            'room_id' => 'nullable|integer',
        ]);

        $type = $request->input('type');
        $orderedIds = array_values(array_map('intval', $request->input('ordered_ids', [])));
        $hotelId = $request->input('hotel_id') ? (int)$request->input('hotel_id') : null;
        $roomId = $request->input('room_id') ? (int)$request->input('room_id') : null;

        try {
            DB::beginTransaction();

            if ($type === 'hotel') {
                $allowed = DB::table('hotel')
                    ->where('vendor_id', $vendorId)
                    ->whereIn('id', $orderedIds)
                    ->pluck('id')
                    ->map(fn ($id) => (int)$id)
                    ->all();

                $allowedLookup = array_fill_keys($allowed, true);
                $pos = 1;
                foreach ($orderedIds as $id) {
                    if (!isset($allowedLookup[$id])) {
                        continue;
                    }
                    DB::table('hotel')->where('id', $id)->update(['ordering' => $pos]);
                    $pos++;
                }
            } elseif ($type === 'room') {
                if (!$hotelId) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'hotel_id is required for room ordering'], 422);
                }

                $allowed = DB::table('rooms')
                    ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
                    ->where('hotel.vendor_id', $vendorId)
                    ->where('rooms.hotel_id', $hotelId)
                    ->whereIn('rooms.id', $orderedIds)
                    ->pluck('rooms.id')
                    ->map(fn ($id) => (int)$id)
                    ->all();

                $allowedLookup = array_fill_keys($allowed, true);
                $pos = 1;
                foreach ($orderedIds as $id) {
                    if (!isset($allowedLookup[$id])) {
                        continue;
                    }
                    DB::table('rooms')->where('id', $id)->update(['ordering' => $pos]);
                    $pos++;
                }
            } else { // room_number
                if (!$hotelId || !$roomId) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'hotel_id and room_id are required for room number ordering'], 422);
                }

                $allowed = DB::table('room_numbers')
                    ->join('hotel', 'room_numbers.hotel_id', '=', 'hotel.id')
                    ->where('hotel.vendor_id', $vendorId)
                    ->where('room_numbers.hotel_id', $hotelId)
                    ->where('room_numbers.room_id', $roomId)
                    ->whereIn('room_numbers.id', $orderedIds)
                    ->pluck('room_numbers.id')
                    ->map(fn ($id) => (int)$id)
                    ->all();

                $allowedLookup = array_fill_keys($allowed, true);
                $pos = 1;
                foreach ($orderedIds as $id) {
                    if (!isset($allowedLookup[$id])) {
                        continue;
                    }
                    DB::table('room_numbers')->where('id', $id)->update(['ordering' => $pos]);
                    $pos++;
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ordering updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating ordering: ' . $e->getMessage()], 500);
        }
    }
}

