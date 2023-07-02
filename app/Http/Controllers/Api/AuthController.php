<?php

namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\BaseController;  
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Models\Temp;
use App\Models\User; 
use Illuminate\Http\Request; 
use Exception; 
use Validator;

class AuthController extends BaseController
{
    // SEND OTP FOR LOGIN WITH EMAIL OR LOGIN WITH PHONE, RESEND OTP

    public function sendOtp(Request $request){
        try{
            
            $otp    = substr(number_format(time() * rand(),0,'',''),0,4);
            $data   = [];
            $data['otp'] = (int)$otp;

            if(isset($request->email)){
                $validateData = Validator::make($request->all(), [ 
                    'email' => 'required|email',
                    'type' => 'required',
                ]);
                
                if ($validateData->fails()) {
                    return $this->error($validateData->errors(),'Validation error',422);
                } 
                
                $key          = $request->email;
                $email_data   = [
                    'email'   => $key,
                    'otp'     => $otp,
                    'subject' => 'Email OTP Verification - For Ecommerce',
                ];
                
                Helper::sendMail('emails.email_verify', $email_data, $key, '');
                
                if(User::where('email', '=', $key)->count() == 0 && $request->type == 'login'){
                    $can_not_find = "Sorry we can not find user with this email";
                    return $this->error($can_not_find,$can_not_find);
                }

            } else if(isset($request->phone_no)){
                
                $validateData = Validator::make($request->all(), [
                    'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                    'type' => 'required',
                ]);

                if ($validateData->fails()) {
                    return $this->error($validateData->errors(),'Validation error',422);
                } 
               
                $key             = $request->phone_no;

                if(User::where('phone_no','=', $key)->count() == 0 && $request->type == 'login'){
                    $can_not_find = "Sorry we can not find user with this phone";
                    return $this->error($can_not_find,$can_not_find);
                }

            } else {
                return $this->error('Please enter email or phone number','Required parameter');
            }
            
            $temp         = Temp::firstOrNew(['key' => $key]);
            $temp->key    = $key;
            $temp->value  = $otp;
            $temp->save();
            
            return $this->success($data,'OTP send successfully');

        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // VERIFY OTP

    public function verifyOtp(Request $request){
        try{
            if(!isset($request->email) && !isset($request->phone_no)){
                return $this->error('Please enter email or phone number','Required parameter');
            }

            $validateData = Validator::make($request->all(), [
                'email' => 'nullable|email',
                'phone_no' => 'nullable|string|max:13',
                'otp' => 'required',
                'type' => 'required',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 

            $key = '';
            if(isset($request->email)){
                $key  = $request->email;
            } else if(isset($request->phone_no)){
                $key  = $request->phone_no;
            } 

            $temp         = Temp::where('key',$key)->first();
            if($temp != null){
                $is_data_present = Temp::where('key',$key)->where('value',$request->otp)->first();
                if($is_data_present != null){
                    $is_data_present->delete();
                }
                return $this->error('OTP is wrong','OTP is wrong');
            }
            $can_not_find = "Sorry we can not find data with this credentials";
            return $this->error($can_not_find,$can_not_find);

                    // $data = [];
                    // $data['user_id'] = 0;
                    // $data['is_user_exist'] = 0;
                    // $data['is_email_verified'] = 0;
                    // $data['otp'] = (int)$request->otp;

                    // $user = User::where('email', '=', $request->email_or_phone)
                    //         ->orWhere('phone_no','=', $request->email_or_phone)
                    //         ->select('id','email', 'phone_no','email_verified')
                    //         ->first();

                    // if ($user) {
                    //     $data['user_id'] = $user->id;
                    //     $data['email'] = $user->email;
                        
                    //     if ($user->email == $request->email_or_phone) {
                    //         $user->email_verified = 1;
                    //     }
                    //     $user->otp_verified = 1;
                    //     $user->save();

                    //     $user->tokens()->delete();
                    //     $data['token'] = $user->createToken('Auth token')->accessToken;
                    // } 
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // USER REGISTRATION

    public function register(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'first_name'      => 'required|string|max:255',
                'last_name'       => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email|max:255',
                'phone_no'        => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,phone_no|max:13', 
                'password'        => 'required|string|min:8|bail|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }   

            $input                   = $request->all();
            $input['user_type']      = 'user';
            $input['password']       = bcrypt($request->password);
            $user_data               = User::create($input);
            $user_data['token']      = $user_data->createToken('Auth token')->accessToken;

            return $this->success($user_data,'You are successfully registered');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

      // USER LOGIN

      public function loginWithPassword(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'email'           => 'required|email|max:255',
                'password'        => 'required|string|min:8|bail|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }   

            if (User::where('email', '=', $request->email)->count() == 0) {
                $can_not_find = "Sorry we can not find user with this email";
                return $this->error($can_not_find,$can_not_find);
            }

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user_data = Auth::user();
                $user_data['token']      = $user_data->createToken('Auth token')->accessToken;
                return $this->success($user_data,'You are successfully logged in');
            }
            return $this->error('OTP is wrong','Password is wrong');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // CHECK EMAIL & PHONE EXISTS OR NOT DURING REGISTRATION 

    public function isExist(Request $request){
        try{

            if(!isset($request->email) && !isset($request->phone_no)){
                return $this->error('Please enter email or phone number','Required parameter');
            }

            $validateData = Validator::make($request->all(), [
                'email' => 'nullable|email',
                'phone_no' => 'nullable|string|max:13',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            } 
            
            if(isset($request->email)){
                $data['is_email_exist'] = 0;
                $key  = $request->email;
                if (User::where('email', '=', $key)->count() > 0) {
                    $data['is_email_exist'] = 1;
                }
                return $this->success($data,'Email exists check');
            }
            
            if(isset($request->phone_no)){
                $data['is_phone_exist'] = 0;
                $key  = $request->phone_no;
                if (User::where('phone_no', '=', $key)->count() > 0) {
                    $data['is_phone_exist'] = 1;
                }
                return $this->success($data,'Phone exists check');
            }
            return $this->success([],'Email or phone exists check');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // FORGOT PASSWORD

    public function forgotPassword(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'email'  => 'required|email|max:255',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }   

            if (User::where('email', '=', $request->email)->count() == 0) {
                $can_not_find = "Sorry we can not find user with this email";
                return $this->error($can_not_find,$can_not_find);
            }

            $key          = $request->email;
            $email_data   = [
                'email'   => $key,
                'otp'     => 12,
                'subject' => 'Email OTP Verification - For Ecommerce',
            ];
            Helper::sendMail('emails.email_verify', $email_data, $key, '');

            return $this->success([],'Reset password link successfully sent to email');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
}
