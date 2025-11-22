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
use Session;
use Validator;
use Mail;
use URL;
use Cookie;
use Illuminate\Validation\Rule;

class UsersController extends Controller{

    private static $UserModel;
    private static $TokenHelper;

    public function __construct(){
        self::$UserModel = new User();
		self::$TokenHelper = new TokenHelper();
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

	# admin dashboard page
    public function calendar(Request $request){
		if(!$request->session()->has('vendor_login_id')){return redirect('/');}
        return view('/calendar/index');
    }

    # admin dashboard page
    public function logout(Request $request){
        $request->session()->flush();
        return redirect('/');
   }

}