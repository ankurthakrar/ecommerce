<?php

namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\BaseController;  
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Helper;
use App\Models\Temp;
use App\Models\User; 
use Illuminate\Http\Request; 
use Exception; 
use Validator;
use DB;

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
                    'subject' => 'Email OTP Verification - For '.config('app.admin_mail'),
                ];
                
                Helper::sendMail('emails.email_verify', $email_data, $key, '');
                $data['email'] = $request->email;
                
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
                $data['phone_no'] = $request->phone_no;
                if(User::where('phone_no','=', $key)->count() == 0 && $request->type == 'login'){
                    $can_not_find = "Sorry we can not find user with this phone";
                    return $this->error($can_not_find,$can_not_find);
                }

                $apiKey       = urlencode(config('app.txt_lcl_api'));
                $senderID     = urlencode(config('app.txt_lcl_sender')); 
                // $apiKey       = urlencode('NDE2NDYzNDM3YTc1NTY3MTU1NjU3NDY5MzAzMDczMzI=');
                // $senderID     = urlencode('HBSEPL'); 
                $message      = "Use OTP $otp for your Hub Sports account. Enter this OTP on the website to verify your mobile.";
                $key      = "+91".$key;

                // API URL
                $url = "https://api.textlocal.in/send";

                // Prepare the data for the API request
                $data = [
                    'apikey' => $apiKey,
                    'sender' => $senderID,
                    'message' => $message,
                    'numbers' => $key,
                ];

                // Make the API request using cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                curl_close($ch);

                // Process the API response
                $responseData = json_decode($response, true);
                if ($responseData && isset($responseData['status']) && strtolower($responseData['status']) !== 'success') {
                    return $this->error('Something went wrong','Something went wrong');
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

                    $data = [];
                    $data['user_id'] = 0;
                    $data['is_user_exist'] = 0;
                    
                    if ($request->type == 'login') {
                        $user = User::where('email', '=', $request->email)
                        ->orWhere('phone_no','=', $request->phone_no)
                        ->select('id','email', 'phone_no')
                        ->first();

                        if($user){
                            $data['user_id'] = $user->id;
                            $data['token'] = $user->createToken('Auth token')->accessToken;
                            $data['is_user_exist'] = 1;
                            return $this->success($data,'Login successfully');
                        }
                        return $this->error('OTP verified successfully but no user exist','OTP verified successfully but no user exist');
                    } 
                    
                    if ($request->type == 'register') {
                        $validateData = Validator::make($request->all(), [
                            'email'    => 'nullable|email|unique:users,email|max:255',
                            'phone_no' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,phone_no|max:13', 
                        ]);
            
                        if ($validateData->fails()) {
                            return $this->error($validateData->errors(),'Validation error',422);
                        } 
            
                        $input                   = $request->all();
                        $input['first_name']     = null;
                        $input['last_name']      = null;
                        $input['email']          = isset($request->email) ? $request->email : null;
                        $input['phone_no']       = isset($request->phone_no) ? $request->phone_no : null;
                        $input['user_type']      = 'user';
                        $input['password']       = bcrypt($request->password);
                        $user_data               = User::create($input);
                        $user_data['is_user_exist'] = 1;
                        $user_data['token']      = $user_data->createToken('Auth token')->accessToken;
                        return $this->success($user_data,'Register successfully');
                    } 
                }
                return $this->error('OTP is wrong','OTP is wrong');
            }
            $can_not_find = "Sorry we can not find data with this credentials";
            return $this->error($can_not_find,$can_not_find);

        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // USER REGISTRATION WITHOUT OTP

    public function registerWithPassword(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'first_name'      => 'required|string|max:255',
                'last_name'       => 'required|string|max:255',
                'email'           => 'required|email|unique:users,email|max:255',
                'phone_no'        => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,phone_no|max:13', 
                'password'        => 'required|string|min:8|bail|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/',
                'confirm_password' => 'required|same:password',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }   

            $input                   = $request->all();
            $input['user_type']      = 'user';
            $input['password']       = bcrypt($request->password);
            $user_data               = User::create($input);
            $user_data['token']      = $user_data->createToken('Auth token')->accessToken;

            $key = $request->email;
            $email_data   = [
                'email'          => $key,
                'welcome_user'   => 'welcome',
                'subject'        => 'Welcome to '.config('app.admin_mail'),
                'user'           => $request->all(),
            ];
            Helper::sendMail('emails.welcome_user', $email_data, $key, '');

            return $this->success($user_data,'You are successfully registered');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // USER LOGIN

    public function loginWithPassword(Request $request){
        try{

            if(!isset($request->email) && !isset($request->phone_no)  && !isset($request->email_or_phone)){
                return $this->error('Please enter email or phone number','Required parameter');
            }
            
            $validateData = Validator::make($request->all(), [
                'email'     => 'nullable|email|max:255',
                'phone_no'  => 'nullable|string|max:13',
                'email_or_phone' => 'required',
                'password'  => 'required|string|min:8|bail|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }   

            if (isset($request->email) && User::where('email', '=', $request->email)->count() == 0) {
                $can_not_find = "Sorry we can not find user with this email";
                return $this->error($can_not_find,$can_not_find);
            }

            if (isset($request->phone_no) && User::where('phone_no', '=', $request->phone_no)->count() == 0) {
                $can_not_find = "Sorry we can not find user with this phone no";
                return $this->error($can_not_find,$can_not_find);
            }
          
            if (isset($request->email_or_phone) && User::where('email', '=', $request->email_or_phone)->orWhere('phone_no', '=', $request->email_or_phone)->count() == 0) {
                $can_not_find = "Sorry we can not find user with this email or phone no";
                return $this->error($can_not_find,$can_not_find);
            }

            $credentials = $request->only('email','phone_no','email_or_phone', 'password');

            if (!isset($request->email_or_phone) && Auth::attempt($credentials)) {
                $user_data = Auth::user();
                $user_data['token']      = $user_data->createToken('Auth token')->accessToken;
                return $this->success($user_data,'You are successfully logged in');
            }else{
                $credentials = $request->only('password');
                $user = User::where(function ($query) use ($request) {
                    $query->where('email', $request->email_or_phone)
                        ->orWhere('phone_no', $request->email_or_phone);
                })->first();

                if ($user && Hash::check($request->password, $user->password)) {
                    $user_data = $user;
                    $user_data['token'] = $user->createToken('Auth token')->accessToken;
                    return $this->success($user_data, 'You are successfully logged in');
                }
                return $this->error('Password is wrong','Password is wrong');
            }
            return $this->error('Password is wrong','Password is wrong');
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
                'email' => 'nullable|email|max:255',
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
            $token        = \Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $key,
                'token' => $token,
                'created_at' => now(),
            ]);

            $email_data   = [
                'email'          => $key,
                'reset_link'     => url('reset-password') . '?token=' . $token,
                'subject'        => 'Reset password link',
            ];
            Helper::sendMail('emails.reset_password', $email_data, $key, '');

            return $this->success([],'Reset password link successfully sent to email');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }

    // RESET PASSWORD
    
    public function resetPassword(Request $request)
    {
        try {
            $validateData = Validator::make($request->all(), [
                'token' => 'required',
                'password' => 'required|string|min:8|bail|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$%^&+=!]).*$/',
                'confirm_password' => 'required|same:password',
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(), 'Validation error', 422);
            }

            $token = $request->token;
            $email = DB::table('password_resets')->where('token', $token)->value('email');

            if (!$email) {
                return $this->error('Invalid token', 'Invalid token');
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return $this->error('User not found', 'User not found');
            }

            $user->password = bcrypt($request->password);
            $user->save();

            DB::table('password_resets')->where('email', $email)->delete();

            return $this->success([], 'Password reset successful');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 'Exception occurred');
        }
    }

    // SOCIAL AUTH

    public function socialAuth(Request $request){
        try{
            $validateData = Validator::make($request->all(), [
                'given_name'      => 'required|string|max:255',
                'family_name'     => 'required|string|max:255',
                'email'           => 'required|email|max:255',
                'google_id'       => 'required', 
            ]);

            if ($validateData->fails()) {
                return $this->error($validateData->errors(),'Validation error',422);
            }   

            $is_user_exist = User::where('email',$request->email)->where('google_id',$request->google_id)->first();
            if(!empty($is_user_exist)){
                $data['token'] = $is_user_exist->createToken('Auth token')->accessToken;
                return $this->success($data,'Login successfully');
            }
            
            $input                   = $request->all();
            $input['user_type']      = 'user';
            $input['first_name']     = $request->given_name;
            $input['last_name']      = $request->family_name;
            $input['google_id']      = $request->google_id;
            $input['email']          = $request->email;
            $user_data               = User::create($input);
            $user_data['token']      = $user_data->createToken('Auth token')->accessToken;

            $key = $request->email;
            $email_data   = [
                'email'          => $key,
                'welcome_user'   => 'welcome',
                'subject'        => 'Welcome to '.config('app.admin_mail'),
                'user'           => $input,
            ];
            Helper::sendMail('emails.welcome_user', $email_data, $key, '');

            return $this->success($user_data,'You are successfully registered');
        }catch(Exception $e){
            return $this->error($e->getMessage(),'Exception occur');
        }
        return $this->error('Something went wrong','Something went wrong');
    }
}
