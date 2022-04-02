<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\users\ChronicDisease;
use App\Models\users\Diagnosis;
use App\Models\users\User;
use App\Models\users\UserChronicDisease;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use GeneralTrait;
        public function getPersonalInfo(Request $request){
            $token = $request -> header('auth-token');
            if($token){
                try {

                   $user = Auth::guard('user-api')->user();
                   $dateOfBirth= $user->date_of_birth;
                   $user->age = calc_age($dateOfBirth);
                }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                    return  $this -> returnError('','some thing went wrongs');
                }
                return $this->returnData('user', $user);
                }else{
                    $this -> returnError('','some thing went wrongs');
                }
        }
        public function editUserinfo(Request $request){
            try{
                // validation
                $userId = Auth::guard('user-api')->user()->id;
                $rules = [
                    "full_name" => "required|regex:/^[\pL\s\-]+$/u",
                    'national_id'=> "required|regex:/^[0-9]+$/|max:14|min:14|unique:users,national_id,".$userId,
                    'mobile'=>"required|regex:/^[0-9]+$/|min:4|max:11|unique:users,mobile,".$userId,
                    'address'=>"required|exists:provinces,name|string",
                    'date_of_birth'=>"required",
                    'blood_type'=>"required|string|in:A+,O+,B+,AB+,A-,O-,B-,AB-",
                    'sex'=>"required|in:male,female,other",
                    'social_status'=>"required|string|in:single,married",

                ];
                $messages = [
                    "required"=>"this filed is Required",
                    "string"=>"this filed must be letters",
                    "in"=>"this value is not in the list",
                    "exists"=>"this province is not in the list",
                    "full_name.regex"=>"this filed must be letters",
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

                $userId = Auth::guard('user-api')->user()->id;
                $user = User::find($userId);
                if(!$user){
                    return $this->returnError('','This user not found');
                }
                $user->update([
                    "full_name"=>$request->full_name,
                    'national_id'=>$request->national_id,
                    'mobile'=>$request->mobile,
                    'address'=>$request->address,
                    'date_of_birth'=>$request->date_of_birth,
                    'blood_type'=>$request->blood_type,
                    'sex'=>$request->sex,
                    'social_status'=>$request->social_status,
                ]);
                $user->save();
                return $this->returnSuccessMessage("Your Information has been Updated Successfully");

            }catch(Exception $ex){
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }

        }
        public function addProfilePicture(Request $request){
            try{
                //update user data to add user photo
                $user_id = Auth::guard('user-api')->user()->id;
                $user = User::find($user_id)/*where('id' , $user_id)->get()*/;
                if($request->has('photo')){
                    $filePath = uploadImage('profile_image' , $request->photo);
                    // $user->photo = $filePath ;
                    $user->update([
                        "photo"=>$filePath,
                    ]);
                    $user->save();
                    // return Success Message
                    $msg = "profile picture updated successfully";
                    return $this->returnSuccessMessage($msg);
                 }else{
                     return $this->returnError('' , 'sorry profile picture has not been updated');
                 }

            }catch(Exception $ex){
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        }
        public function addChronicDisease(Request $request){
                try{
                //validation
                $rules = [
                    'chronic_disease_id'=> "required|exists:chronic_diseases,id",
                    // 'user_id'=> "required|exists:users,id",
                ];
                $messages = [
                    "required"=>"this filed is Required",
                    "chronic_disease_id.exists"=>"this chronic disease is not in the list"
                ];
                $validator = Validator::make($request->all(), $rules , $messages);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                //for not repeate the disease
                $user_id = Auth::guard('user-api')->user()->id;
                $chronic_diseases = UserChronicDisease::where('user_id' ,$user_id)->get();
                $data = [];
                foreach ($chronic_diseases as $chronic_disease) {
                    $data[] = $chronic_disease->chronic_disease_id;
                }
                if(in_array($request->chronic_disease_id , $data)){
                    $msg = "this disease was previously added";
                    return $this->returnError('401' , $msg);
                }else{
                     UserChronicDisease::create([
                        'chronic_disease_id'=>$request->chronic_disease_id,
                        'user_id'=>$user_id
                      ]);
                      //return Success Message
                      $msg = "Disease has been added successfully";
                      return $this->returnSuccessMessage($msg);
                }
            }catch(Exception $ex){
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        }
        public function getUserChronicDisease(Request $request){
            $token = $request -> header('auth-token');
            if($token){
                try {

                   $user_id = Auth::guard('user-api')->user()->id;
                    $user = User::find($user_id);
                   $chronic_disease = $user->userChronicDiseases;
                }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                    return  $this -> returnError('','some thing went wrongs');
                }
                if($chronic_disease->isEmpty()){
                    return $this->returnError('', 'you don`t have any chronic disease');
                }
                return $this->returnData('chronic_disease', $chronic_disease);
                }else{
                  return  $this -> returnError('','some thing went wrongs');
                }
        }
        public function addDiagnosis(Request $request){
            try {
                //validation
                $rules = [
                    "diagnosis"=>"required|string"
                ];
                $messages = [
                    "required"=>"this filed is Required",
                    "string"=>"this filed must be letters",
                ];
                $validator = Validator::make($request->all(), $rules , $messages);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                $user_id = Auth::guard('user-api')->user()->id;
                Diagnosis::create([
                    "diagnosis"=>$request->diagnosis,
                    "user_id"=>$user_id
                ]);
                //return success messege
                $msg = "Diagnosis has been added successfully";
                return $this->returnSuccessMessage($msg);
            } catch (Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        }
        public function editDiagnosis(Request $request){
            try {
                //validation
                $rules = [
                    "diagnosis"=>"required|string"
                ];
                $messages = [
                    "required"=>"this filed is Required",
                    "string"=>"this filed must be letters",
                ];
                $validator = Validator::make($request->all(), $rules , $messages);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                $user_id = Auth::guard('user-api')->user()->id;
                $diagnosis = Diagnosis::find($request->id);
                if(!$diagnosis){
                    return $this->returnError('' , 'this chronic diagnosis doesn`t exists');
                }
                $diagnosis->update([
                    "diagnosis"=>$request->diagnosis,
                    "user_id"=>$user_id
                ]);
                //return success messege
                $msg = "Diagnosis has been updated successfully";
                return $this->returnSuccessMessage($msg);
            } catch (Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        }
        public function getUserDiagnosis(Request $request){
            $token = $request -> header('auth-token');
            if($token){
                try {
                   $user_id = Auth::guard('user-api')->user()->id;
                    $user = User::find($user_id);
                   $diagnosis = $user->diagnosises;
                }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                    return  $this -> returnError('','some thing went wrongs');
                }
                if($diagnosis->isEmpty()){
                    return $this->returnError('', 'you don`t have any diagnosises');
                }
                return $this->returnData('diagnosis', $diagnosis);
                }else{
                    $this -> returnError('','some thing went wrongs');
                }
        }
        public function changeDiseaseStatus(Request $request){
            $token = $request -> header('auth-token');
            if($token){
                try {

                    $userdisease = UserChronicDisease::find($request->id);
                    if(!$userdisease){
                        return $this->returnError('',"thid disease does not exist");
                    }
                    if($userdisease->status == 1){
                        $userdisease->status =0;
                    }else{
                        $userdisease->status =1;
                    }
                    $userdisease->save();
                    // return $this->returnData('d' , $userdisease->status);
                    return $this->returnSuccessMessage("status changed successfully");
                    }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                        return  $this -> returnError('','some thing went wrongs');
                    }
                }else{
                    $this -> returnError('','some thing went wrongs');
                }
        }
        // public function deleteDisease(Request $request){
        //     $token = $request->header('auth-token');
        //     if($token){
        //         try{
        //             $id = $request->id;
        //             if($id != null){
        //                 $disease = UserChronicDisease::find($id);
        //                 if(!$disease){
        //                    return $this->returnError('' , 'this chronic desease doesn`t exists');
        //                 }
        //                 $disease->delete();
        //                return $this->returnSuccessMessage('Chronic disease removed successfuly');
        //             }else{
        //                return $this->returnError('' , 'something went wrongs');
        //             }

        //         }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
        //            return $this->returnError('' , 'something went wrongs');
        //         }
        //     }else{
        //        return $this->returnError('' , 'something went wrongs');
        //     }
        // }
}
