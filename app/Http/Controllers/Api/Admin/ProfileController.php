<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\admins\Advice;
use App\Models\users\ChronicDisease;
use App\Models\users\User;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use GeneralTrait;
    //start advice section
    public function addAdvice(Request $request){
        try {
            //validation
            $rules = [
                "advice"=>"required|string",
                "status"=>"required|in:0,1",
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
            Advice::create([
                "advice"=>$request->advice,
                "status"=>$request->status,
            ]);
            //return success messege
            $msg = "advice has been added successfully";
            return $this->returnSuccessMessage($msg);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function editAdvice(Request $request){
        try {
            //validation
            $rules = [
                "advice"=>"required|string",
                "status"=>"required|in:0,1",
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
            $advice = Advice::find($request->id);
            if(!$advice){
                return $this->returnError('' , 'this advice doesn`t exists');
            }
            $advice->update([
                "advice"=>$request->advice,
                "status"=>$request->status,
            ]);
            //return success messege
            $msg = "advice has been updated successfully";
            return $this->returnSuccessMessage($msg);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function deleteAdvice(Request $request){
        $token = $request->header('auth-token');
            if($token){
                try{
                    $adviceId = $request->id;
                    if($adviceId != null){
                        $advice = Advice::find($adviceId);
                        if(!$advice){
                           return $this->returnError('' , 'this advice doesn`t exists');
                        }
                        $advice->delete();
                       return $this->returnSuccessMessage('advice removed successfuly');
                    }else{
                       return $this->returnError('' , 'something went wrongs');
                    }

                }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                   return $this->returnError('' , 'something went wrongs');
                }
            }else{
               return $this->returnError('' , 'something went wrongs');
            }
    }
    public function adviceStatus (Request $request){
        $token = $request->header('auth-token');
            if($token){
                try{
                    $adviceId = $request->id;
                    if($adviceId != null){
                        $advice = Advice::find($adviceId);
                        if(!$advice){
                           return $this->returnError('' , 'this advice doesn`t exists');
                        }
                        if($advice->status==0){
                           $advice->update([
                               'status'=>1
                           ]);
                        }else{
                            $advice->update([
                                'status'=>0
                            ]);
                        }

                       return $this->returnSuccessMessage('advice status change successfuly');
                    }else{
                       return $this->returnError('' , 'something went wrongs');
                    }

                }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                   return $this->returnError('' , 'something went wrongs');
                }
            }else{
               return $this->returnError('' , 'something went wrongs');
            }
    }
    //end advice section
    //start chronic diseases section
    public function addChronicDisease(Request $request){
        try {
            //validation
            $rules = [
                "chronic_disease"=>"required|string",
                "description"=>"required|regex:/^[a-zA-Z]+$/u",
                "treatment"=>"required|regex:/^[a-zA-Z]+$/u",
                "syndrome"=>"required|regex:/^[a-zA-Z]+$/u",
            ];
            $messages = [
                "required"=>"this filed is Required",
                "string"=>"this filed must be letters",
                "regex"=>"this filed must be letters",
            ];
            $validator = Validator::make($request->all(), $rules , $messages);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            ChronicDisease::create([
                "chronic_disease"=>$request->chronic_disease,
                "description"=>$request->description,
                "treatment"=>$request->treatment,
                "syndrome"=>$request->syndrome,
            ]);
            //return success messege
            $msg = "chronic disease has been added successfully";
            return $this->returnSuccessMessage($msg);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function editChronicDisease(Request $request){
        try {
            //validation
            $rules = [
                "chronic_disease"=>"required|string",
                "description"=>"required|regex:/^[a-zA-Z]+$/u",
                "treatment"=>"required|regex:/^[a-zA-Z]+$/u",
                "syndrome"=>"required|regex:/^[a-zA-Z]+$/u",
            ];
            $messages = [
                "required"=>"this filed is Required",
                "string"=>"this filed must be letters",
                "regex"=>"this filed must be letters",
            ];
            $validator = Validator::make($request->all(), $rules , $messages);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $disease = ChronicDisease::find($request->id);
            if(!$disease){
                return $this->returnError('' , 'this chronic disease doesn`t exists');
            }
            $disease->update([
                "chronic_disease"=>$request->chronic_disease,
                "description"=>$request->description,
                "treatment"=>$request->treatment,
                "syndrome"=>$request->syndrome,
            ]);
            //return success messege
            $msg = "chronic disease has been updated successfully";
            return $this->returnSuccessMessage($msg);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function deleteChronicDisease(Request $request){
        $token = $request->header('auth-token');
            if($token){
                try{
                    $chronic_diseaseId = $request->id;
                    if($chronic_diseaseId != null){
                        $chronic_disease = ChronicDisease::find($chronic_diseaseId);
                        if(!$chronic_disease){
                           return $this->returnError('' , 'this chronic disease doesn`t exists');
                        }
                        $chronic_disease->delete();
                       return $this->returnSuccessMessage('chronic disease removed successfuly');
                    }else{
                       return $this->returnError('' , 'something went wrongs');
                    }

                }catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                   return $this->returnError('' , 'something went wrongs');
                }
            }else{
               return $this->returnError('' , 'something went wrongs');
            }
    }
    //end chronic diseases section
    //search about user
    public function userSearch(Request $request){
        try {
              //validation
              $rules = [
                "national_id"=>"required|max:14",
            ];
            $messages = [
                "required"=>"this filed is Required",
                "national_id.max"=>"the national number must be 14 characters long",
            ];
            $validator = Validator::make($request->all(), $rules , $messages);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $user = User::where('national_id' , '=' ,$request->national_id)->get();
            if($user->isEmpty()){
                return  $this -> returnError('','Sorry, this user not found');
            }
            return $this->returnData('user' , $user);

        }catch(Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function getAllAdvices(){
        try {
            $advices = Advice::all();
            if($advices->isEmpty()){
                return  $this -> returnError('','Sorry, there are no advices');
            }
            return $this->returnData('Donors' , $advices);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function delete(){
        ChronicDisease::all()->delete();
    }

}
