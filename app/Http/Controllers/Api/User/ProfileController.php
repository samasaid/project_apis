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
        public function addProfilePicture(Request $request){
            try{
                //validation
                $rules = [
                    'photo'=>"mimes:jpg,jpeg,png",
                ];
                $messages = [
                    "mimes"=>"the image extention must be png or jpg or jpeg",
                ];
                $validator = Validator::make($request->all(), $rules , $messages);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                //update user data to add user photo
                $user_id = Auth::guard('user-api')->user()->id;
                $user = User::where('id' , $user_id)->first();
                if($request->has('photo')){
                    $filePath = uploadImage('profile_image' , $request->photo);
                    $user->photo = $filePath ;
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
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                $user_id = Auth::guard('user-api')->user()->id;
                $diagnosis = Diagnosis::find($request->id);
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
