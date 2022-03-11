<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admins\Admin;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use GeneralTrait;
    public function login(Request $request){
        try {
            //validation
            $rules = [
                'username'=> "required|min:5",
                "password"=>"required"
            ];
            $messages = [
                "required"=>"this filed is Required",
                "username.min"=>"the user name must be longer than 5 characters long",
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            // login

            $credentials = $request->only(['username', 'password']);
            $token = Auth::guard('admin-api')->attempt($credentials);
            if (!$token)
                return $this->returnError('E001', 'the login information is incorrect, try again');
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
