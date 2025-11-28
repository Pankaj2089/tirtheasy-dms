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
    public function __construct(){
        self::$UserModel = new User();
		self::$TokenHelper = new TokenHelper();
		self::$HotelsModel = new Hotels();
		self::$RoomsModel = new Rooms();
		self::$RoomPricesModel = new RoomPrices();
		self::$RoomNumbersModel = new RoomNumbers();
		self::$MaintenanceBlocksModel = new MaintenanceBlocks();
        self::$OrdersModel = new Orders();
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

    private function buildRoomsPayload($vendorId, $startDate = null, $endDate = null, $preloadedBookings = null, $hotelId = null){
        [$rangeStartIso, $rangeEndIso] = $this->resolveCalendarRange($startDate, $endDate);
        $rangeStart = Carbon::parse($rangeStartIso)->startOfDay();
        $rangeEnd = Carbon::parse($rangeEndIso)->startOfDay();
        $dateSequence = $this->buildDateSequence($rangeStart, $rangeEnd);
        $bookings = $preloadedBookings ?? $this->buildBookingsPayload($vendorId, $rangeStartIso, $rangeEndIso, $hotelId);
        $bookingLookup = $this->buildRoomBookingLookup($bookings, $rangeStart, $rangeEnd);
        $rooms = [];
        DB::statement("SET SQL_MODE=''");
        $recQry = self::$HotelsModel
            ->join('rooms', 'hotel.id', '=', 'rooms.hotel_id')
            ->where('hotel.vendor_id', $vendorId)
            ->select([
                'hotel.*',
                DB::raw('MIN(rooms.price) as lowest_price'),
                'rooms.title as room_title',
                'rooms.id as room_id'
            ])
            ->where('hotel.status', 1);
        
        if($hotelId){
            $recQry->where('hotel.id', $hotelId);
        }
        
        $recQry->groupBy('hotel.id');

        $hotels = $recQry->get();
        if($hotels->count() > 0){
            foreach($hotels as $hotelData){
                $roomNumbers = self::$RoomNumbersModel
                    ->where('status', 1)
                    ->where('room_id', $hotelData->room_id)
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

                    $rooms[] = [
                        'hostelName' => $hotelData->title,
                        'title' => $hotelData->room_title,
                        'summary' => [
                            'defaultCount' => $totalRooms,
                            'defaultPrice' => $hotelData->lowest_price,
                            'tooltip' => 'Available rooms / base rate',
                            'overrides' => $overrides,
                        ],
                        'rooms' => $roomNums,
                    ];
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
            $roomId = $booking['room_id'] ?? $booking['roomId'] ?? null;
            $bookingStart = $booking['start_date'] ?? $booking['startDate'] ?? null;
            $bookingEnd = $booking['end_date'] ?? $booking['endDate'] ?? $bookingStart;
            if(!$roomId || !$bookingStart){
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

            $cursor = $start->copy();
            while($cursor->lte($end)){
                if($cursor->lt($rangeStart)){
                    $cursor->addDay();
                    continue;
                }
                if($cursor->gt($rangeEnd)){
                    break;
                }
                $lookup[$roomId][$cursor->toDateString()] = true;
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

    private function buildBookingsPayload($vendorId, $startDate = null, $endDate = null, $hotelId = null){
        $bookings = [];
        $blocksQuery = self::$MaintenanceBlocksModel
            ->join('room_numbers', 'maintenance_blocks.room_number_id', '=', 'room_numbers.id')
            ->join('rooms', 'room_numbers.room_id', '=', 'rooms.id')
            ->where('maintenance_blocks.vendor_id', $vendorId);
        
        if($hotelId){
            $blocksQuery->where('rooms.hotel_id', $hotelId);
        }

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
            ->where('hotel.vendor_id', $vendorId);
        
        if($hotelId){
            $ordersQuery->where('hotel.id', $hotelId);
        }

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
            'orders.created_at'
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
                ];
            }
        }

        return $bookings;
    }

    private function getVendorHotels($vendorId){
        $hotels = self::$HotelsModel
            ->where('vendor_id', $vendorId)
            ->where('status', 1)
            ->select('id', 'title')
            ->orderBy('title', 'ASC')
            ->get();
        return $hotels;
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

		return view('/calendar/index', compact('rooms', 'bookings', 'quickReservationRooms', 'hotels'));
    }

    public function calendarData(Request $request){
        if(!$request->session()->has('vendor_login_id')){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $hotelId = $request->query('hotel_id');

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

        [$rangeStart, $rangeEnd] = $this->resolveCalendarRange($start, $end);
        $bookings = $this->buildBookingsPayload($vendorId, $rangeStart, $rangeEnd, $hotelIdFilter);
        $rooms = $this->buildRoomsPayload($vendorId, $rangeStart, $rangeEnd, $bookings, $hotelIdFilter);

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
                $grandTotal = $subTotal;
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
            }

            DB::commit();
        }catch(\Throwable $throwable){
            DB::rollBack();
            report($throwable);
            return response()->json([
                'success' => false,
                'message' => 'Unable to save reservation right now. Please try again.'
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

}