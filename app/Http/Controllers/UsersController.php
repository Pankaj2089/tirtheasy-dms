<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use ReallySimpleJWT\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Str;
use App\Models\Languages;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\RoomPrices;
use App\Models\RoomNumbers;
use App\Models\MaintenanceBlocks;
use App\Models\Orders;
use App\Models\OrderPayments;
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller{

    private const DEFAULT_VIEW_SPAN_DAYS = 9;

    private static $UserModel;
    private static $TokenHelper;
	private static $HotelsModel;
	private static $RoomsModel;
	private static $RoomPricesModel;
	private static $RoomNumbersModel;
	private static $MaintenanceBlocksModel;
    private static $OrdersModel;
    private static $OrderPaymentsModel;
    public function __construct(){
        self::$UserModel = new User();
		self::$TokenHelper = new TokenHelper();
		self::$HotelsModel = new Hotels();
		self::$RoomsModel = new Rooms();
		self::$RoomPricesModel = new RoomPrices();
		self::$RoomNumbersModel = new RoomNumbers();
		self::$MaintenanceBlocksModel = new MaintenanceBlocks();
        self::$OrdersModel = new Orders();
        self::$OrderPaymentsModel = new OrderPayments();
    }
	
    #login page
    public function LoginPage(Request $request){
        if($request->session()->has('vendor_login_id')){
            return redirect('/calendar');
        }
        return view('/users/login');
    }

	# vendor login api
    public function VendorLogin(Request $request){
		$validator = Validator::make($request->all(), [
			'property_code' => 'required',
			'login_id' => 'required',
			'password' => 'required',
		],[
			'property_code.required' => 'Please enter your property code.',
			'login_id.required' => 'Please enter your login ID.',
			'password.required' => 'Please enter your password.'
		]);

		if($validator->fails()){
			 $errors = $validator->errors();
			if($errors->first('property_code')){
				echo json_encode(array('heading'=>'Error Property Code','msg'=>$errors->first('property_code')));
			}else if($errors->first('login_id')){
				echo json_encode(array('heading'=>'Error login_id','msg'=>$errors->first('login_id')));
			}else if($errors->first('password')){
				echo json_encode(array('heading'=>'Error Password','msg'=>$errors->first('password')));
			}
		}else{
			if(isset($request->reminderMe) && $request->reminderMe == 1){
                Cookie::queue('cookiePropertyCode', $request->property_code, 5000);
                Cookie::queue('cookieLoginId', $request->login_id, 5000);
                Cookie::queue('cookiePassword', $request->password, 5000);
            }else{
                Cookie::queue('cookiePropertyCode', "", 5000);
                Cookie::queue('cookieLoginId', '', 5000);
                Cookie::queue('cookiePassword', '', 5000);
            }

			$User = self::$UserModel->where('property_id', $request->property_code)->where('login_id', $request->login_id)->where('status',1)->where('type',"Vendor")->first();
			if($User){
				$PasswordMatch = password_verify($request->password, $User->password);
				if(!$PasswordMatch){
					echo json_encode(array('heading'=>'Error Account','msg'=>'Username and password incorrect'));
				}else{
					session(['vendor_id' => $User->id, 'vendor_login_id' => $User->login_id, 'vendor_profile' => $User, 'vendor_type' => $User->type, 'vendor_name' => $User->name]);
					echo json_encode(array('heading'=>'Success','msg'=>''));
				}
			}else{
				echo json_encode(array('heading'=>'Error Account','msg'=>'Property Code, Username and password incorrect'));
			}
		}
    }

    private function buildRoomsPayload($vendorId, $startDate = null, $endDate = null, $preloadedBookings = null, $hotelId = null, $roomCategoryId = null, $roomId = null){
        [$rangeStartIso, $rangeEndIso] = $this->resolveCalendarRange($startDate, $endDate);
        $rangeStart = Carbon::parse($rangeStartIso)->startOfDay();
        $rangeEnd = Carbon::parse($rangeEndIso)->startOfDay();
        $dateSequence = $this->buildDateSequence($rangeStart, $rangeEnd);
        $bookings = $preloadedBookings ?? $this->buildBookingsPayload($vendorId, $rangeStartIso, $rangeEndIso, $hotelId, $roomCategoryId);
        $bookingLookup = $this->buildRoomBookingLookup($bookings, $rangeStart, $rangeEnd);
        $rooms = [];
        DB::statement("SET SQL_MODE=''");
        
        // First get all hotels
        $hotelsQuery = self::$HotelsModel
            ->where('vendor_id', $vendorId)
            ->where('status', 1);
        
        if($hotelId){
            $hotelsQuery->where('id', $hotelId);
        }
        
        $hotels = $hotelsQuery->get();
        
        if($hotels->count() > 0){
            foreach($hotels as $hotelData){
                // Get all rooms for this hotel (ignore room category - work based on individual rooms)
                $roomsQuery = self::$RoomsModel
                    ->where('hotel_id', $hotelData->id)
                    ->where('status', 1);
                
                $hotelRooms = $roomsQuery->get();
                
                // Process each room type for this hotel
                foreach($hotelRooms as $roomData){
                    // Filter by room_id if specified
                    if($roomId && $roomData->id != $roomId){
                        continue;
                    }
                    
                    $roomNumbers = self::$RoomNumbersModel
                        ->where('status', 1)
                        ->where('room_id', $roomData->id)
                        ->get();
                    
                    if($roomNumbers->count() > 0){
                        $roomNums = [];
                        foreach($roomNumbers as $roomNumberData){
                            $roomNums[] = [
                                'id' => $roomNumberData->id,
                                'number' => $roomNumberData->full_title
                            ];
                        }

                        $totalRooms = count($roomNums);
                        $overrides = [];
                        foreach($dateSequence as $isoDate){
                            $bookedCount = 0;
                            foreach($roomNums as $roomMeta){
                                $roomNumberId = $roomMeta['id'];
                                if(isset($bookingLookup[$roomNumberId]) && isset($bookingLookup[$roomNumberId][$isoDate])){
                                    $bookedCount++;
                                }
                            }
                            $available = max($totalRooms - $bookedCount, 0);
                            $overrides[$isoDate] = [
                                'count' => $available,
                                'tooltip' => sprintf('%d of %d rooms available', $available, $totalRooms)
                            ];
                        }

                        // Get lowest price for this room type
                        $lowestPrice = self::$RoomPricesModel
                            ->where('room_id', $roomData->id)
                            ->where('status', 1)
                            ->min('price');
                        
                        if(!$lowestPrice){
                            $lowestPrice = $roomData->price ?? null;
                        }

                        $rooms[] = [
                            'hostelName' => $hotelData->title,
                            'title' => $roomData->title,
                            'roomId' => $roomData->id, // Add room type ID for filtering
                            'summary' => [
                                'defaultCount' => $totalRooms,
                                'defaultPrice' => $lowestPrice,
                                'tooltip' => 'Available rooms / base rate',
                                'overrides' => $overrides,
                            ],
                            'rooms' => $roomNums,
                        ];
                    }
                }
            }
        }
        DB::statement("SET SQL_MODE=only_full_group_by");

        return $rooms;
    }

    private function buildDateSequence(Carbon $start, Carbon $end){
        $dates = [];
        $cursor = $start->copy();
        while($cursor->lte($end)){
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }
        return $dates;
    }

    private function buildRoomBookingLookup(array $bookings, Carbon $rangeStart, Carbon $rangeEnd){
        $lookup = [];
        foreach($bookings as $booking){
            $bookingStart = $booking['start_date'] ?? $booking['startDate'] ?? null;
            $bookingEnd = $booking['end_date'] ?? $booking['endDate'] ?? $bookingStart;
            if(!$bookingStart){
                continue;
            }
            
            // Get specific room number IDs if available
            $roomNumberIds = [];
            if(!empty($booking['room_number_ids'])){
                $roomNumberIdsString = $booking['room_number_ids'];
                if(is_string($roomNumberIdsString)){
                    $roomNumberIds = array_filter(array_map(function($id) {
                        return (int)trim($id);
                    }, explode(',', $roomNumberIdsString)));
                } elseif(is_array($roomNumberIdsString)){
                    $roomNumberIds = array_map('intval', $roomNumberIdsString);
                }
            }
            
            // For maintenance blocks, room_id is actually the room_number_id
            if(empty($roomNumberIds) && isset($booking['is_blocked']) && $booking['is_blocked']){
                $roomId = $booking['room_id'] ?? $booking['roomId'] ?? null;
                if($roomId){
                    $roomNumberIds = [(int)$roomId];
                }
            }
            
            // If no specific room numbers, skip this booking (can't determine which rooms are booked)
            if(empty($roomNumberIds)){
                continue;
            }
            
            try{
                $start = Carbon::parse($bookingStart)->startOfDay();
                $end = Carbon::parse($bookingEnd)->startOfDay();
            }catch(\Exception $exception){
                continue;
            }
            if($end->lt($start)){
                [$start, $end] = [$end->copy(), $start->copy()];
            }

            // Check if end date should be included (for maintenance blocks)
            $inclusiveEnd = isset($booking['inclusive_end']) && $booking['inclusive_end'];
            $isBlocked = isset($booking['is_blocked']) && $booking['is_blocked'];
            
            // For regular bookings (check-out date), the room is available again, so exclude the end date
            // For maintenance blocks (inclusive_end = true), include the end date
            $cursor = $start->copy();
            while(true){
                // Check if we should include this date based on inclusive_end flag
                $shouldInclude = $inclusiveEnd || $isBlocked ? $cursor->lte($end) : $cursor->lt($end);
                
                if(!$shouldInclude){
                    break;
                }
                
                if($cursor->lt($rangeStart)){
                    $cursor->addDay();
                    continue;
                }
                if($cursor->gt($rangeEnd)){
                    break;
                }
                
                $dateString = $cursor->toDateString();
                foreach($roomNumberIds as $roomNumberId){
                    $lookup[$roomNumberId][$dateString] = true;
                }
                $cursor->addDay();
            }
        }
        return $lookup;
    }

    private function resolveCalendarRange(?string $startDate = null, ?string $endDate = null){
        try{
            $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        }catch(\Exception $exception){
            $start = null;
        }

        try{
            $end = $endDate ? Carbon::parse($endDate)->startOfDay() : null;
        }catch(\Exception $exception){
            $end = null;
        }

        if(!$start && $end){
            $start = $end->copy()->subDays(self::DEFAULT_VIEW_SPAN_DAYS);
        }

        if(!$start){
            $start = Carbon::now()->startOfWeek(Carbon::MONDAY);
        }

        if(!$end){
            $end = $start->copy()->addDays(self::DEFAULT_VIEW_SPAN_DAYS);
        }

        if($end->lt($start)){
            [$start, $end] = [$end->copy(), $start->copy()];
        }

        return [$start->toDateString(), $end->toDateString()];
    }

    private function buildBookingsPayload($vendorId, $startDate = null, $endDate = null, $hotelId = null, $roomCategoryId = null){
        $bookings = [];
        $blocksQuery = self::$MaintenanceBlocksModel
            ->join('room_numbers', 'maintenance_blocks.room_number_id', '=', 'room_numbers.id')
            ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
            ->where('maintenance_blocks.vendor_id', $vendorId);
        
        if($hotelId){
            $blocksQuery->where('rooms.hotel_id', $hotelId);
        }
        // Removed room_category_id filter - work based on individual rooms

        if($startDate){
            $start = Carbon::parse($startDate)->startOfDay()->toDateString();
        }else{
            $start = null;
        }

        if($endDate){
            $end = Carbon::parse($endDate)->endOfDay()->toDateString();
        }else{
            $end = null;
        }

        if($start && $end){
            $blocksQuery->where(function($query) use ($start, $end){
                $query->whereBetween('maintenance_blocks.start_date', [$start, $end])
                    ->orWhereBetween('maintenance_blocks.end_date', [$start, $end])
                    ->orWhere(function($inner) use ($start, $end){
                        $inner->where('maintenance_blocks.start_date', '<=', $start)
                            ->where('maintenance_blocks.end_date', '>=', $end);
                    });
            });
        }elseif($start){
            $blocksQuery->where('maintenance_blocks.end_date', '>=', $start);
        }elseif($end){
            $blocksQuery->where('maintenance_blocks.start_date', '<=', $end);
        }

        $maintenanceBlocks = $blocksQuery->select('maintenance_blocks.*')->get();
        if($maintenanceBlocks->count() > 0){
            foreach($maintenanceBlocks as $maintenanceBlock){
                $bookings[] = [
                    'id' => 'MB-'.$maintenanceBlock->id,
                    'room_id' => $maintenanceBlock->room_number_id,
                    'start_date' => $maintenanceBlock->start_date,
                    'end_date' => $maintenanceBlock->end_date,
                    'guest_name' => $maintenanceBlock->reason,
                    'label' => 'Maintenance Block',
                    'source' => 'maintenance',
                    'is_blocked' => true,
                    'status' => 'maintenance',
                    'inclusive_end' => true,
                ];
            }
        }

        $ordersQuery = self::$OrdersModel
            ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
            ->join('rooms', 'orders.room_id', '=', 'rooms.id')
            ->where('hotel.vendor_id', $vendorId);
        if($hotelId){
            $ordersQuery->where('hotel.id', $hotelId);
        }
        // Removed room_category_id filter - work based on individual rooms

        if($start && $end){
            $ordersQuery->where(function($query) use ($start, $end){
                $query->whereBetween('orders.check_in_date', [$start, $end])
                    ->orWhereBetween('orders.check_out_date', [$start, $end])
                    ->orWhere(function($inner) use ($start, $end){
                        $inner->where('orders.check_in_date', '<=', $start)
                            ->where('orders.check_out_date', '>=', $end);
                    });
            });
        }elseif($start){
            $ordersQuery->where('orders.check_out_date', '>=', $start);
        }elseif($end){
            $ordersQuery->where('orders.check_in_date', '<=', $end);
        }

        $orders = $ordersQuery->get([
            'orders.id',
            'orders.room_id',
            'orders.check_in_date',
            'orders.check_out_date',
            'orders.billing_name',
            'orders.order_platform',
            'orders.other_details',
            'orders.payment_status',
            'orders.invoice_id',
            'orders.created_at',
            'orders.room_number_ids'
        ]);

        if($orders->count() > 0){
            foreach($orders as $order){
                $details = [];
                if(!empty($order->other_details)){
                    $decoded = json_decode($order->other_details, true);
                    if(json_last_error() === JSON_ERROR_NONE){
                        $details = $decoded;
                    }
                }
                $reservationType = strtolower($details['reservation_type'] ?? '');
                $status = $this->determineBookingStatus($order->payment_status, $reservationType);

                $bookings[] = [
                    'id' => 'ORD-'.$order->id,
                    'room_id' => $order->room_id,
                    'start_date' => $order->check_in_date,
                    'end_date' => $order->check_out_date ?? $order->check_in_date,
                    'guest_name' => $order->billing_name ?? 'Reserved Guest',
                    'label' => $order->billing_name ?? $order->invoice_id ?? 'Reservation',
                    'status' => $status,
                    'source' => $order->order_platform ?? 'DMS',
                    'is_blocked' => false,
                    'inclusive_end' => true,
                    'room_number_ids' => $order->room_number_ids ?? null,
                ];
            }
        }

        return $bookings;
    }

    private function getVendorHotels($vendorId){
        $hotels = self::$HotelsModel
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
        return $hotels;
    }

    private function getVendorRoomCategories($vendorId){
        // Changed to return room types instead of room categories - work based on individual rooms
        // Only show room types that have at least one available room number
        return DB::table('rooms')
            ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
            ->join('room_numbers', 'rooms.id', '=', 'room_numbers.room_id')
            ->where('hotel.vendor_id', $vendorId)
            ->where('rooms.status', 1)
            ->where('room_numbers.status', 1)
            ->select('rooms.id', 'rooms.title')
            ->distinct()
            ->orderBy('rooms.title', 'ASC')
            ->get();
    }

	# admin dashboard page
    public function calendar(Request $request){
		if(!$request->session()->has('vendor_login_id')){return redirect('/');}

        $vendorId = $request->session()->get('vendor_id');
        [$rangeStart, $rangeEnd] = $this->resolveCalendarRange();
        $bookings = $this->buildBookingsPayload($vendorId, $rangeStart, $rangeEnd);
        $rooms = $this->buildRoomsPayload($vendorId, $rangeStart, $rangeEnd, $bookings);
        $quickReservationRooms = $this->buildQuickReservationRooms($vendorId);
        $hotels = $this->getVendorHotels($vendorId);
        $roomCategories = $this->getVendorRoomCategories($vendorId);

		return view('/calendar/index', compact('rooms', 'bookings', 'quickReservationRooms', 'hotels', 'roomCategories'));
    }

    public function calendarData(Request $request){
        if(!$request->session()->has('vendor_login_id')){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $hotelId = $request->query('hotel_id');
        $roomId = $request->query('room_id'); // Changed from room_category_id to room_id

        try{
            $start = $startDate ? Carbon::parse($startDate)->toDateString() : null;
            $end = $endDate ? Carbon::parse($endDate)->toDateString() : null;
        }catch(\Exception $exception){
            return response()->json(['message' => 'Invalid date supplied.'], 422);
        }

        if($start && $end && $start > $end){
            return response()->json(['message' => 'Start date must be before end date.'], 422);
        }

        $hotelIdFilter = null;
        if($hotelId && is_numeric($hotelId)){
            $hotelIdFilter = (int)$hotelId;
        }
        $roomIdFilter = null;
        if($roomId && is_numeric($roomId)){
            $roomIdFilter = (int)$roomId;
        }

        [$rangeStart, $rangeEnd] = $this->resolveCalendarRange($start, $end);
        $bookings = $this->buildBookingsPayload($vendorId, $rangeStart, $rangeEnd, $hotelIdFilter, null); // Removed room category filter
        $rooms = $this->buildRoomsPayload($vendorId, $rangeStart, $rangeEnd, $bookings, $hotelIdFilter, null, $roomIdFilter); // Added room_id filter
        
        // Filter rooms by room_id if specified
        if($roomIdFilter){
            $rooms = array_values(array_filter($rooms, function($room) use ($roomIdFilter) {
                return isset($room['roomId']) && $room['roomId'] == $roomIdFilter;
            }));
        }

        return response()->json([
            'rooms' => $rooms,
            'bookings' => $bookings,
            'range' => [
                'start_date' => $rangeStart,
                'end_date' => $rangeEnd,
            ]
        ]);
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

    public function saveQuickReservation(Request $request){
        if(!$request->session()->has('vendor_login_id')){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $today = date('Y-m-d');

        $validator = Validator::make($request->all(), [
            'check_in_date' => ['required','date_format:Y-m-d','after_or_equal:'.$today],
            'check_out_date' => ['required','date_format:Y-m-d','after_or_equal:check_in_date','after_or_equal:'.$today],
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'reservation_type' => ['required', Rule::in(['confirm','confirmed','hold','inquiry'])],
            'notes' => 'nullable|string|max:500',
            'guest.name' => 'required|string|max:191',
            'guest.gender' => ['nullable', Rule::in(['male','female','other'])],
            'guest.email' => 'nullable|email',
            'guest.mobile' => 'required|string|min:8|max:20',
            'room_details' => 'required|array|min:1',
            'room_details.*.room_id' => 'required|integer|exists:rooms,id',
            'room_details.*.room_price_id' => 'required|integer|exists:room_prices,id',
            'room_details.*.rooms' => 'required|integer|min:1',
            'room_details.*.adults' => 'required|integer|min:1',
            'room_details.*.children' => 'nullable|integer|min:0',
            'room_details.*.price' => 'required|numeric|min:0',
            'room_details.*.room_number_ids' => 'nullable|string',
            // Charges and Payments validation
            'charges' => 'nullable|array',
            'charges.*.comment' => 'required|string|max:255',
            'charges.*.amount' => 'required|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.type' => 'required|string|in:Cash,Online',
            'payments.*.method' => 'required|string|max:50',
            'payments.*.comment' => 'nullable|string|max:255',
            'payments.*.amount' => 'required|numeric|min:0',
            // Calculated totals
            'room_total' => 'nullable|numeric|min:0',
            'total_charges' => 'nullable|numeric|min:0',
            'total_payments' => 'nullable|numeric|min:0',
            'grand_total' => 'nullable|numeric|min:0',
        ],[
            'check_in_date.required' => 'Please select check-in date.',
            'check_in_date.after_or_equal' => 'Check-in date cannot be in the past.',
            'check_out_date.after_or_equal' => 'Check-out date cannot be before the check-in date or in the past.',
            'guest.name.required' => 'Guest name is required.',
            'guest.mobile.required' => 'Guest mobile is required.',
            'room_details.required' => 'Please add at least one room.',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->all();
        
        // Debug: Log incoming charges and payments
        \Log::info('Quick Reservation Charges:', $input['charges'] ?? []);
        \Log::info('Quick Reservation Payments:', $input['payments'] ?? []);
        
        $vendorId = $request->session()->get('vendor_id');
        $roomDetails = collect($input['room_details']);
        $roomIds = $roomDetails->pluck('room_id')->unique()->values();

        $rooms = DB::table('rooms')
            ->join('hotel','rooms.hotel_id','=','hotel.id')
            ->where('hotel.vendor_id', $vendorId)
            ->whereIn('rooms.id', $roomIds)
            ->select([
                'rooms.id',
                'rooms.title',
                'rooms.hotel_id',
                'rooms.no_of_rooms',
                'hotel.title as hotel_title'
            ])
            ->get()
            ->keyBy('id');

        if($rooms->count() !== $roomIds->count()){
            return response()->json([
                'success' => false,
                'message' => 'One or more room selections are invalid for your property.'
            ], 422);
        }

        $priceIds = $roomDetails->pluck('room_price_id')->unique()->values();
        $roomPrices = self::$RoomPricesModel
            ->whereIn('id', $priceIds)
            ->where('status', 1)
            ->get()
            ->keyBy('id');

        foreach($roomDetails as $detail){
            $priceRow = $roomPrices->get($detail['room_price_id']);
            if(!$priceRow || (int)$priceRow->room_id !== (int)$detail['room_id']){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid rate plan selected for one of the rooms.'
                ], 422);
            }
        }

        $checkInDate = Carbon::parse($input['check_in_date'])->toDateString();
        $checkOutDate = Carbon::parse($input['check_out_date'])->toDateString();
        $arrival = Carbon::parse($checkInDate);
        $departure = Carbon::parse($checkOutDate);
        $nightCount = max(1, $arrival->diffInDays($departure) ?: 1);

        $guestPayload = $input['guest'];
        $guestQuery = self::$UserModel->newQuery();
        $guestQuery->where(function($query) use ($guestPayload){
            if(!empty($guestPayload['mobile'])){
                $query->where('mobile', $guestPayload['mobile']);
            }
            if(!empty($guestPayload['email'])){
                $query->orWhere('email', $guestPayload['email']);
            }
        });
        $guestUser = $guestQuery->first();

        if(!$guestUser){
            $newGuest = [
                'type' => 'Guest',
                'vendor_id' => $vendorId,
                'name' => $guestPayload['name'],
                'email' => $guestPayload['email'] ?? null,
                'mobile' => $guestPayload['mobile'],
                'gender' => $guestPayload['gender'] ?? null,
                'password' => Hash::make(Str::random(12)),
            ];
            $guestUser = self::$UserModel->CreateRecord($newGuest);
        }else{
            $guestUser->name = $guestPayload['name'];
            if(!empty($guestPayload['gender'])){
                $guestUser->gender = $guestPayload['gender'];
            }
            if(!empty($guestPayload['email'])){
                $guestUser->email = $guestPayload['email'];
            }
            if(!empty($guestPayload['mobile'])){
                $guestUser->mobile = $guestPayload['mobile'];
            }
            $guestUser->save();
        }

        $priceBookingLookup = [];
        if($roomPrices->count() > 0){
            $overlappingOrders = self::$OrdersModel
                ->whereIn('room_price_id', $priceIds)
                ->where(function($query) use ($checkInDate, $checkOutDate){
                    $query->whereBetween('check_in_date', [$checkInDate, $checkOutDate])
                        ->orWhereBetween('check_out_date', [$checkInDate, $checkOutDate])
                        ->orWhere(function($inner) use ($checkInDate, $checkOutDate){
                            $inner->where('check_in_date', '<=', $checkInDate)
                                ->where('check_out_date', '>=', $checkOutDate);
                        });
                })
                ->get(['room_price_id','rooms']);

            foreach($overlappingOrders as $order){
                $priceBookingLookup[$order->room_price_id] = ($priceBookingLookup[$order->room_price_id] ?? 0) + (int)$order->rooms;
            }
        }

        foreach($roomDetails as $detail){
            $priceRow = $roomPrices->get($detail['room_price_id']);
            $existingBooked = $priceBookingLookup[$priceRow->id] ?? 0;
            $inventory = (int)($priceRow->no_of_rooms ?? 0);
            if($inventory <= 0){
                $inventory = (int)($rooms[$detail['room_id']]->no_of_rooms ?? 0);
            }
            if($inventory > 0 && ((int)$detail['rooms'] + $existingBooked) > $inventory){
                return response()->json([
                    'success' => false,
                    'message' => 'Only '.$inventory.' room(s) are available for '.$rooms[$detail['room_id']]->title.'.'
                ], 422);
            }
            $priceBookingLookup[$priceRow->id] = $existingBooked + (int)$detail['rooms'];
        }

        $reservationType = strtoupper($input['reservation_type']);
        $createdOrders = [];
        $totalAmount = 0;
        
        // Get charges and payments from input
        $charges = $input['charges'] ?? [];
        $payments = $input['payments'] ?? [];
        $totalChargesAmount = collect($charges)->sum('amount');
        $totalPaymentsAmount = collect($payments)->sum('amount');

        DB::beginTransaction();
        try{
            $lastOrder = DB::table('orders')->select('in_no','id')->orderBy('id','DESC')->first();
            $nextInvoiceNo = $lastOrder && isset($lastOrder->in_no) ? ((int)$lastOrder->in_no + 1) : 1;

            foreach($roomDetails as $index => $detail){
                $pricePerNight = (float)$detail['price'];
                $roomsCount = (int)$detail['rooms'];
                $adults = (int)$detail['adults'];
                $children = (int)($detail['children'] ?? 0);
                $subTotal = $pricePerNight * $roomsCount * $nightCount;
                
                // Calculate grand total including charges
                $grandTotal = $subTotal + $totalChargesAmount;
                $totalAmount += $grandTotal;

                $paddedNo = str_pad($nextInvoiceNo, 3, "0", STR_PAD_LEFT);
                $invoiceId = date('Y').'/'.date('m').'/N'.$paddedNo;
                $nextInvoiceNo++;

                $otherDetails = [
                    'reservation_type' => $reservationType,
                    'check_in_time' => $input['check_in_time'] ?? null,
                    'check_out_time' => $input['check_out_time'] ?? null,
                    'notes' => $input['notes'] ?? null,
                    'source' => 'quick_reservation',
                    'line_index' => $index,
                    'requested_rate' => [
                        'room_price_id' => (int)$detail['room_price_id'],
                        'price_entered' => $pricePerNight,
                    ],
                ];

                $orderPayload = [
                    'invoice_id' => $invoiceId,
                    'in_no' => $paddedNo,
                    'vendor_id' => $vendorId,
                    'user_id' => $guestUser->id,
                    'hotel_id' => $rooms[$detail['room_id']]->hotel_id,
                    'room_id' => (int)$detail['room_id'],
                    'room_price_id' => (int)$detail['room_price_id'],
                    'adults' => $adults,
                    'childs' => $children,
                    'rooms' => $roomsCount,
                    'number_of_nights' => $nightCount,
                    'extra_mattress' => 0,
                    'extra_mattress_price' => 0,
                    'room_price' => $pricePerNight,
                    'sub_total' => $subTotal,
                    'grand_total' => $grandTotal,
                    'discount' => 0,
                    'convenience' => 0,
                    'coupon_code' => null,
                   // 'cod_amount' => null,
                    'billing_name' => $guestPayload['name'],
                    'billing_email' => $guestPayload['email'] ?? null,
                    'billing_phone' => $guestPayload['mobile'],
                    'billing_address' => null,
                    'billing_city' => null,
                    'billing_state' => null,
                    'room_number_ids' => !empty($detail['room_number_ids']) ? $detail['room_number_ids'] : null,
                    'payment_status' => 'PENDING',
                    'payment_method_type' => 'OFFLINE',
                    'order_platform' => 'DMS',
                    'platform' => 'dms',
                    'check_in_date' => $checkInDate,
                    'check_out_date' => $checkOutDate,
                    'txn_id' => null,
                    'other_details' => json_encode($otherDetails),
                ];

                $orderRecord = self::$OrdersModel->CreateRecord($orderPayload);
                $createdOrders[] = [
                    'order_id' => $orderRecord->id,
                    'invoice_id' => $invoiceId,
                    'amount' => $grandTotal,
                    'room_title' => $rooms[$detail['room_id']]->title
                ];
                
                // Save charges to order_payments table (only for first order)
                if($index === 0 && !empty($charges)){
                    foreach($charges as $charge){
                        self::$OrderPaymentsModel->CreateRecord([
                            'order_id' => $orderRecord->id,
                            'type' => 'Charge',
                            'comment' => $charge['comment'] ?? null,
                            'amount' => $charge['amount'],
                        ]);
                    }
                }
                
                // Save payments to order_payments table (only for first order)
                if($index === 0 && !empty($payments)){
                    foreach($payments as $payment){
                        self::$OrderPaymentsModel->CreateRecord([
                            'order_id' => $orderRecord->id,
                            'type' => 'Payment',
                            'payment_type' => $payment['type'],
                            'payment_method' => $payment['method'],
                            'comment' => $payment['comment'] ?? null,
                            'amount' => $payment['amount'],
                        ]);
                    }
                }
            }

            DB::commit();
        }catch(\Throwable $throwable){
            DB::rollBack();
            report($throwable);
            return response()->json([
                'success' => false,
                'message' => 'Unable to save reservation right now. Please try again.',
                'error' => $throwable->getMessage(),
                'line' => $throwable->getLine(),
                'file' => basename($throwable->getFile())
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reservation saved successfully.',
            'data' => [
                'total_amount' => $totalAmount,
                'orders' => $createdOrders
            ]
        ]);
    }

    public function getOrderPayments($orderId){
        try {
            \Log::info('Fetching order payments for order_id: ' . $orderId);
            
            $payments = self::$OrderPaymentsModel->GetRecords([
                'order_id' => $orderId
            ]);
            
            \Log::info('Found payments count: ' . $payments->count());
            \Log::info('Payments data: ' . json_encode($payments));
            
            return response()->json([
                'success' => true,
                'payments' => $payments
            ]);
        } catch(\Throwable $throwable) {
            \Log::error('Error fetching order payments: ' . $throwable->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment history',
                'error' => $throwable->getMessage(),
                'line' => $throwable->getLine(),
                'file' => basename($throwable->getFile())
            ], 500);
        }
    }

    public function saveBookingChargesPayments(Request $request){
        if(!$request->session()->has('vendor_login_id')){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        \Log::info('saveBookingChargesPayments Request:', $request->all());

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'charges' => 'nullable|array',
            'charges.*.comment' => 'required|string',
            'charges.*.amount' => 'required|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.type' => 'required|in:Cash,Online',
            'payments.*.method' => 'required|string',
            'payments.*.comment' => 'nullable|string',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $orderId = $request->order_id;
            $charges = $request->charges ?? [];
            $payments = $request->payments ?? [];
            
            \Log::info('Processing order_id: ' . $orderId);
            \Log::info('Charges count: ' . count($charges));
            \Log::info('Payments count: ' . count($payments));
            
            $totalCharges = 0;
            $totalPayments = 0;

            // Save charges
            foreach($charges as $charge){
                self::$OrderPaymentsModel->CreateRecord([
                    'order_id' => $orderId,
                    'type' => 'Charge',
                    'payment_type' => null,
                    'payment_method' => null,
                    'comment' => $charge['comment'],
                    'amount' => $charge['amount'],
                ]);
                $totalCharges += $charge['amount'];
            }

            // Save payments
            foreach($payments as $payment){
                self::$OrderPaymentsModel->CreateRecord([
                    'order_id' => $orderId,
                    'type' => 'Payment',
                    'payment_type' => $payment['type'],
                    'payment_method' => $payment['method'],
                    'comment' => $payment['comment'] ?? null,
                    'amount' => $payment['amount'],
                ]);
                $totalPayments += $payment['amount'];
            }

            // Update order grand_total
            $order = self::$OrdersModel->GetRecordById($orderId);
            \Log::info('Order found:', ['order' => $order]);
            
            if($order){
                $newGrandTotal = $order->grand_total + $totalCharges;
                
                \Log::info('Updating order:', [
                    'old_grand_total' => $order->grand_total,
                    'total_charges' => $totalCharges,
                    'new_grand_total' => $newGrandTotal,
                    'total_payments' => $totalPayments
                ]);
                
                self::$OrdersModel->UpdateRecord([
                    'id' => $orderId,
                    'grand_total' => $newGrandTotal,
                ]);
                
                \Log::info('Order updated successfully');
            } else {
                \Log::warning('Order not found for id: ' . $orderId);
            }

            DB::commit();
            \Log::info('Transaction committed successfully');

            return response()->json([
                'success' => true,
                'message' => 'Charges and payments saved successfully'
            ]);
        } catch(\Throwable $throwable) {
            DB::rollBack();
            report($throwable);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save charges and payments',
                'error' => $throwable->getMessage(),
                'line' => $throwable->getLine(),
                'file' => basename($throwable->getFile())
            ], 500);
        }
    }

    # admin dashboard page
    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/');
   }

    private function determineBookingStatus($paymentStatus, $reservationType = null){
        $type = strtolower($reservationType ?? '');
        if(in_array($type, ['hold', 'inquiry'])){
            return $type;
        }
        $payment = strtolower($paymentStatus ?? '');
        if($payment === 'pending'){
            return 'due';
        }
        if(in_array($payment, ['cancelled', 'failed'])){
            return 'cancelled';
        }
        return 'reserved';
    }

    public function getBookingDetails(Request $request, $id){
        if(!$request->session()->has('vendor_login_id')){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        
        // Check if it's a maintenance block (MB- prefix) or order (ORD- prefix)
        if(strpos($id, 'MB-') === 0){
            $maintenanceBlockId = (int)str_replace('MB-', '', $id);
            $maintenanceBlock = self::$MaintenanceBlocksModel
                ->join('room_numbers', 'maintenance_blocks.room_number_id', '=', 'room_numbers.id')
                ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
                ->join('hotel', 'rooms.hotel_id', '=', 'hotel.id')
                ->where('maintenance_blocks.id', $maintenanceBlockId)
                ->where('maintenance_blocks.vendor_id', $vendorId)
                ->select('maintenance_blocks.*', 'room_numbers.full_title as room_number', 'rooms.title as room_type', 'hotel.title as hotel_name')
                ->first();
            
            if(!$maintenanceBlock){
                return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
            }
            
            return response()->json([
                'success' => true,
                'booking' => [
                    'id' => 'MB-'.$maintenanceBlock->id,
                    'type' => 'maintenance',
                    'guest_name' => $maintenanceBlock->reason,
                    'room_number' => $maintenanceBlock->room_number,
                    'room_type' => $maintenanceBlock->room_type,
                    'hotel_name' => $maintenanceBlock->hotel_name,
                    'check_in_date' => $maintenanceBlock->start_date,
                    'check_out_date' => $maintenanceBlock->end_date,
                    'created_at' => $maintenanceBlock->created_at ?? $maintenanceBlock->start_date,
                ]
            ]);
        } else if(strpos($id, 'ORD-') === 0){
            $orderId = (int)str_replace('ORD-', '', $id);
            $order = self::$OrdersModel
                ->join('hotel', 'orders.hotel_id', '=', 'hotel.id')
                ->join('rooms', 'orders.room_id', '=', 'rooms.id')
                ->where('orders.id', $orderId)
                ->where('hotel.vendor_id', $vendorId)
                ->select('orders.*', 'rooms.title as room_type', 'hotel.title as hotel_name')
                ->first();
            
            if(!$order){
                return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
            }
            
            // Get room numbers with full details from room_number_ids
            $roomNumbers = [];
            $roomNumbersDetails = [];
            if($order->room_number_ids){
                $roomNumberIds = explode(',', $order->room_number_ids);
                $roomNumberRecords = self::$RoomNumbersModel
                    ->whereIn('id', $roomNumberIds)
                    ->where('status', 1)
                    ->get(['id', 'full_title', 'title', 'prefix']);
                $roomNumbers = $roomNumberRecords->pluck('full_title')->toArray();
                $roomNumbersDetails = $roomNumberRecords->map(function($room) {
                    return [
                        'id' => $room->id,
                        'full_title' => $room->full_title,
                        'title' => $room->title,
                        'prefix' => $room->prefix
                    ];
                })->toArray();
            }
            
            $otherDetails = [];
            if(!empty($order->other_details)){
                $decoded = json_decode($order->other_details, true);
                if(json_last_error() === JSON_ERROR_NONE){
                    $otherDetails = $decoded;
                }
            }
            
            return response()->json([
                'success' => true,
                'booking' => [
                    'id' => 'ORD-'.$order->id,
                    'order_id' => $order->id,
                    'type' => 'reservation',
                    'invoice_id' => $order->invoice_id,
                    'guest_name' => $order->billing_name ?? 'Guest',
                    'guest_email' => $order->billing_email,
                    'guest_phone' => $order->billing_phone,
                    'room_numbers' => $roomNumbers,
                    'room_numbers_details' => $roomNumbersDetails,
                    'room_type' => $order->room_type,
                    'hotel_name' => $order->hotel_name,
                    'check_in_date' => $order->check_in_date,
                    'check_out_date' => $order->check_out_date,
                    'check_in_time' => $otherDetails['check_in_time'] ?? null,
                    'check_out_time' => $otherDetails['check_out_time'] ?? null,
                    'adults' => $order->adults,
                    'children' => $order->childs ?? 0,
                    'rooms' => $order->rooms,
                    'number_of_nights' => $order->number_of_nights,
                    'room_price' => $order->room_price,
                    'sub_total' => $order->sub_total,
                    'discount' => $order->discount ?? 0,
                    'grand_total' => $order->grand_total,
                    'payment_status' => $order->payment_status,
                    'reservation_type' => $otherDetails['reservation_type'] ?? 'confirmed',
                    'notes' => $otherDetails['notes'] ?? null,
                    'created_at' => $order->created_at,
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Invalid booking ID'], 400);
    }

}