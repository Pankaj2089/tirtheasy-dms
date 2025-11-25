<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\MaintenanceBlocks;
use App\RouteHelper;
use App\Models\TokenHelper;
use App\Models\Responses;
use ReallySimpleJWT\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Str;
use App\Models\Languages;
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;

class MaintenanceBlockController extends Controller{   

    private static $MaintenanceBlocks;
    private static $TokenHelper;

    public function __construct(){
        self::$MaintenanceBlocks = new MaintenanceBlocks();
		self::$TokenHelper = new TokenHelper();
    }
	

	# save maintenance block api
    public function saveMaintenanceBlock(Request $request){
		$validator = Validator::make($request->all(), [
			'room_number_id' => 'required',
			'start_date' => 'required',
			'end_date' => 'required',
			'reason' => 'required',
		],[
			'room_number_id.required' => 'Please select a room.',
			'start_date.required' => 'Please select a start date.',
			'end_date.required' => 'Please select an end date.',
			'reason.required' => 'Please enter a reason.',
		]);

		if($validator->fails()){
			 $errors = $validator->errors();
			if($errors->first('room_number_id')){
				echo json_encode(array('heading'=>'Error Property Code','msg'=>$errors->first('property_code')));
			}else if($errors->first('start_date')){
				echo json_encode(array('heading'=>'Error start_date','msg'=>$errors->first('start_date')));
			}else if($errors->first('end_date')){
				echo json_encode(array('heading'=>'Error end_date','msg'=>$errors->first('end_date')));
			}else if($errors->first('reason')){
				echo json_encode(array('heading'=>'Error reason','msg'=>$errors->first('reason')));
			}
		}else{
			$MaintenanceBlock = self::$MaintenanceBlocks->where('room_number_id', $request->room_number_id)->where('start_date', $request->start_date)->where('end_date', $request->end_date)->first();
			if($MaintenanceBlock){
				echo json_encode(array('heading'=>'Error Maintenance Block','msg'=>'Maintenance block already exists'));
			}else{
				$MaintenanceBlock = self::$MaintenanceBlocks->create([
					'room_number_id' => $request->room_number_id,
					'start_date' => $request->start_date,
					'end_date' => $request->end_date,
					'reason' => $request->reason,   
					'vendor_id' => $request->session()->get('vendor_login_id'),
				]);
				echo json_encode(array('heading'=>'Success','msg'=>'Maintenance block created successfully'));
			}
		}
	}
}