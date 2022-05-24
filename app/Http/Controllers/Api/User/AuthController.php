<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\users\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\GeneralTrait;


class AuthController extends Controller
{
    use GeneralTrait;
        public function register(Request $request){
                try{
                // validation
                    $rules = [
                        "full_name" => "required|regex:/^[\pL\s\-]+$/u",
                        'national_id'=> "required|regex:/^[0-9]+$/|unique:users,national_id|max:14|min:14",
                        'mobile'=>"required|regex:/^[0-9]+$/|unique:users,mobile|min:4|max:11",
                        'address'=>"required|exists:provinces,name|string",
                        'date_of_birth'=>"required",
                        'blood_type'=>"required|string|in:A+,O+,B+,AB+,A-,O-,B-,AB-",
                        'sex'=>"required|in:male,female,other",
                        'social_status'=>"required|string|in:single,married",

                    ];
                    $messages = [
                        "required"=>"this filed is Required",
                        "full_name.regex"=>"this filed must be letters",
                        "in"=>"this value is not in the list",
                        "exists"=>"this province is not in the list",
                        "mobile.regex"=>"this filed shoud be numeric",
                        "national_id.regex"=>"this filed shoud be numeric",
                        "mobile.min"=>"the mobile content very short",
                        "national_id.min"=>"the national number content very short",
                        "national_id.unique"=>"the national number has already been registered",
                        "national_id.max"=>"the national number must be 14 characters long",
                        "mobile.unique"=>"the mobile number has already been registered",

                    ];

                    $validator = Validator::make($request->all(), $rules , $messages);

                    if ($validator->fails()) {
                        $code = $this->returnCodeAccordingToInput($validator);
                        return $this->returnValidationError($code, $validator);
                    }

                    //register
                    $user = User::create([
                        'full_name'=>$request->full_name,
                        'national_id'=>$request->national_id,
                        'mobile'=>$request->mobile,
                        'address'=>$request->address,
                        'date_of_birth'=>$request->date_of_birth,
                        'blood_type'=>$request->blood_type,
                        'sex'=>$request->sex,
                        'social_status'=>$request->social_status,
                    ]);
                    $token = JWTAuth::fromUser($user);
                    if (!$token)
                         return $this->returnError('E001', 'an error Occurred , try again');

                    //return token
                    $msg = "your health history has been successfully created";
                    return $this->returnData('token', $token , $msg);
            }catch(Exception $ex){
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        }
        public function login(Request $request){
            try {
                //validation
                $rules = [
                    'national_id'=> "required|regex:/^[0-9]+$/|max:14|min:14",
                ];
                $messages = [
                    "required"=>"this filed is Required",
                    "national_id.max"=>"the national number must be 14 characters long",
                    "national_id.regex"=>"this filed shoud be numeric",
                    "national_id.min"=>"the national number content very short",
                ];
                $validator = Validator::make($request->only(['national_id']), $rules , $messages);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                // login

                // $credentials = $request->only(['national_id']);
                $national_id = $request->input('national_id');
                $userLogin = User::where('national_id' , '=' , $national_id)->first();
                if ($userLogin == null){
                    return $this->returnError('E001', 'the national number is incorrect, try again');
                }
                $token = JWTAuth::fromUser($userLogin);
                //return token
                $msg = "you are loggin successfully";
                return $this->returnData('token', $token , $msg);
            } catch (Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());

            }
        }
        public function logout(Request $request){
            $token = $request -> header('auth-token');
            if($token){
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnSuccessMessage('Logged out successfully');
            }else{
                $this -> returnError('','some thing went wrongs');
            }

        }
}
