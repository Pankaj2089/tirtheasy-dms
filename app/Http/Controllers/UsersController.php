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
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class UsersController extends Controller{

    private static $UserModel;
    private static $TokenHelper;
	private static $HotelsModel;
	private static $RoomsModel;
	private static $RoomPricesModel;
	private static $RoomNumbersModel;
	private static $MaintenanceBlocksModel;
    public function __construct(){
        self::$UserModel = new User();
		self::$TokenHelper = new TokenHelper();
		self::$HotelsModel = new Hotels();
		self::$RoomsModel = new Rooms();
		self::$RoomPricesModel = new RoomPrices();
		self::$RoomNumbersModel = new RoomNumbers();
		self::$MaintenanceBlocksModel = new MaintenanceBlocks();
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

	private function buildRoomsPayload($vendorId){
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
            ->where('hotel.status', 1)
            ->groupBy('hotel.id');

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

                    $rooms[] = [
                        'hostelName' => $hotelData->title,
                        'title' => $hotelData->room_title,
                        'summary' => [
                            'defaultCount' => 0,
                            'defaultPrice' => $hotelData->lowest_price,
                            'tooltip' => 'Available villas / base rate',
                            'overrides' => [
                                '2025-11-24' => ['count' => 0, 'price' => 2000],
                                '2025-11-25' => ['count' => 2, 'price' => 2000],
                                '2025-11-26' => ['count' => 1, 'price' => 2000],
                            ],
                        ],
                        'rooms' => $roomNums,
                    ];
                }
            }
        }
        DB::statement("SET SQL_MODE=only_full_group_by");

        return $rooms;
    }

    private function buildBookingsPayload($vendorId, $startDate = null, $endDate = null){
        $bookings = [];
        $blocksQuery = self::$MaintenanceBlocksModel->where('vendor_id', $vendorId);

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
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function($inner) use ($start, $end){
                        $inner->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            });
        }elseif($start){
            $blocksQuery->where('end_date', '>=', $start);
        }elseif($end){
            $blocksQuery->where('start_date', '<=', $end);
        }

        $maintenanceBlocks = $blocksQuery->get();
        if($maintenanceBlocks->count() > 0){
            foreach($maintenanceBlocks as $maintenanceBlock){
                $bookings[] = [
                    'id' => $maintenanceBlock->id,
                    'room_id' => $maintenanceBlock->room_number_id,
                    'start_date' => $maintenanceBlock->start_date,
                    'end_date' => $maintenanceBlock->end_date,
                    'guest_name' => $maintenanceBlock->reason,
                    'label' => 'Maintenance Block',
                    'is_blocked' => true,
                    'status' => 'maintenance',
                ];
            }
        }

        return $bookings;
    }

	# admin dashboard page
    public function calendar(Request $request){
		if(!$request->session()->has('vendor_login_id')){return redirect('/');}

        $vendorId = $request->session()->get('vendor_id');
        $rooms = $this->buildRoomsPayload($vendorId);
        $bookings = $this->buildBookingsPayload($vendorId);

		return view('/calendar/index', compact('rooms', 'bookings'));
    }

    public function calendarData(Request $request){
        if(!$request->session()->has('vendor_login_id')){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendorId = $request->session()->get('vendor_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        try{
            $start = $startDate ? Carbon::parse($startDate)->toDateString() : null;
            $end = $endDate ? Carbon::parse($endDate)->toDateString() : null;
        }catch(\Exception $exception){
            return response()->json(['message' => 'Invalid date supplied.'], 422);
        }

        if($start && $end && $start > $end){
            return response()->json(['message' => 'Start date must be before end date.'], 422);
        }

        $rooms = $this->buildRoomsPayload($vendorId);
        $bookings = $this->buildBookingsPayload($vendorId, $start, $end);

        return response()->json([
            'rooms' => $rooms,
            'bookings' => $bookings,
            'range' => [
                'start_date' => $start,
                'end_date' => $end,
            ]
        ]);
    }

    # admin dashboard page
    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/');
   }

}