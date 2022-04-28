<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\admins\Admin;
use App\Models\admins\Advice;
use App\Models\users\ChronicDisease;
use App\Models\users\Donor;
use App\Models\users\User;
use Illuminate\Http\Request;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Constraint\Count;

class ProfileController extends Controller
{
    use GeneralTrait;
    //start advice section
    public function addAdvice(Request $request){
        try {
            //validation
            $rules = [
                "advice"=>"required|string",
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
            if ($request->status == null) {
                $request->request->add(['status'=>0]);
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
                // "status"=>"required|in:0,1",
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
            if ($request->status == null) {
                $request->request->add(['status'=>0]);
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
    public function getAllAdvices(){
        try {
            $advices = Advice::all();
            if($advices->isEmpty()){
                return  $this -> returnError('','Sorry, there are no advices');
            }
            return $this->returnData('advices' , $advices);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    //end advice section
    //start chronic diseases section
    public function addChronicDisease(Request $request){
        try {
            //validation
            $rules = [
                "chronic_disease"=>"required|string",
                "description"=>"required",
                "treatment"=>"required",
                "syndrome"=>"required",
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
                "description"=>"required",
                "treatment"=>"required",
                "syndrome"=>"required",
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
                "national_id"=>"required|max:14|min:14",
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

            $user = User::where('national_id' , '=' ,$request->national_id)->get(['id','full_name','national_id','mobile']);
            if($user->isEmpty()){
                return  $this -> returnError('','Sorry, this user not found');
            }
            return $this->returnData('user' , $user );

        }catch(Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    //edit  user national number by admin
    public function editUserId(Request $request){
        try {
            //validation
            $rules = [
                "national_id"=>"required|max:14|min:14",
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
            $user = User::find($request->id);
            if(!$user){
                return $this->returnError('' , 'this user doesn`t exists');
            }
            $user->update([
                "national_id"=>$request->national_id,
            ]);
            //return success messege
            $msg = "user information has been updated successfully";
            return $this->returnSuccessMessage($msg);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    //get and edit and edit admin info
    public function getAdminInfo(Request $request){
        $token = $request -> header('auth-token');
        if($token){
            try {

               $user = Auth::guard('admin-api')->user();
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('','some thing went wrongs');
            }
            return $this->returnData('admin', $user);
            }else{
                $this -> returnError('','some thing went wrongs');
            }
    }
    public function editAdminInfo(Request $request){
        try{
            // validation
            $userId = Auth::guard('admin-api')->user()->id;
            $rules = [
                'name' => "required|string",
                // 'username'=> "required|string|unique:admins,username,".$userId,
                'email'=>"required|email|unique:admins,email,".$userId,

            ];
            $messages = [
                "required"=>"this filed is Required",
                "string"=>"this filed must be letters",
                "username.unique"=>"the username has already been registered",
                "email.unique"=>"the email has already been registered",

            ];

            $validator = Validator::make($request->all(), $rules , $messages);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $userId = Auth::guard('admin-api')->user()->id;
            $user = Admin::find($userId);
            if(!$user){
                return $this->returnError('','This user not found');
            }
            $user->update([
                "name"=>$request->name,
                // 'username'=>$request->username,
                'email'=>$request->email,
            ]);
            $user->save();
            return $this->returnSuccessMessage("Your Information has been Updated Successfully");

        }catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }
    //edit admin profile picture
    public function addProfilePicture(Request $request){
        try{
            //update user data to add user photo
            $user_id = Auth::guard('admin-api')->user()->id;
            $user = Admin::find($user_id);
            if($request->has('photo')){
                $filePath = uploadImage('profile_image' , $request->photo);
                // $user->photo = $filePath ;
                $user->update([
                    "photo"=>$filePath,
                ]);
                $user->save();
                // return Success Message
                $msg = "profile picture updated successfully";
                return $this->returnData('photo',$user->photo,$msg);
             }else{
                 return $this->returnError('' , 'sorry profile picture has not been updated');
             }

        }catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    //get all users
    public function getAllUsers(Request $request){
        try {
            $users = User::all();
            if($users->isEmpty()){
                return  $this -> returnError('','Sorry, there are no users');
            }
            foreach($users as $user) {
                if($user->isOnline()){
                        $user->update([
                            'active'=>1 //online
                        ]);

                }else{
                    $user->update([
                        'active'=>0 //offline
                    ]);
                }
            }
            return $this->returnData('users' , $users);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    //get number of users are make registration per month
    public function getNumOfUserForChart(){
        try{
            $users = User::select('id' , 'created_at')->get()->groupBy( function($date){
                    return  Carbon::parse($date->created_at)->format('m');
            });
            $usermcount = [];
            $userArr = [];
            foreach ($users as $key => $value) {
                $usermcount[ (int)$key ] = count($value);
            }
            $month = ['Jan' , 'Feb' , 'Mar' , 'Apr' , 'May' , 'Jun' , 'Jul' , 'Aug' , 'Sep' , 'Oct' , 'Nov' , 'Dec'];
            for ($i= 1; $i<= 12 ; $i++) {
                if (!empty($usermcount[$i])) {
                    $userArr[$i]['count'] = $usermcount[$i];
                }else {
                    $userArr[$i]['count'] = 0;
                }
                $userArr[$i]['month'] = $month[$i-1];
            }
            return $this->returnData('data' , $userArr);
        }catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
     //get number of donors are added per month
     public function getNumOfDonorForChart(){
        try{
            $donors = Donor::select('id' , 'created_at')->get()->groupBy( function($date){
                    return  Carbon::parse($date->created_at)->format('m');
            });
            $donormcount = [];
            $donorArr = [];
            foreach ($donors as $key => $value) {
                $donormcount[ (int)$key ] = count($value);
            }
            $month = ['Jan' , 'Feb' , 'Mar' , 'Apr' , 'May' , 'Jun' , 'Jul' , 'Aug' , 'Sep' , 'Oct' , 'Nov' , 'Dec'];
            for ($i= 1; $i<= 12 ; $i++) {
                if (!empty($donormcount[$i])) {
                    $donorArr[$i]['count'] = $donormcount[$i];
                }else {
                    $donorArr[$i]['count'] = 0;
                }
                $donorArr[$i]['month'] = $month[$i-1];
            }
            return $this->returnData('data' , $donorArr);
        }catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    // function that use to get number of users and chronic diseases and advice and donors
    public function getCount(){
        try {
            $users = \App\Models\users\User::count();
            $donors = \App\Models\users\Donor::count();
            $diseases = \App\Models\users\ChronicDisease::count();
            $advices = \App\Models\admins\Advice::count();
            $counts = [
                'users'=>$users,
                'donors'=>$donors,
                'diseases'=>$diseases,
                'advices'=>$advices
            ];
            return $this->returnData('counts' , $counts);
        } catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    //function for delete users
    public function deleteUser(Request $request){
        $token = $request->header('auth-token');
            if($token){
                try{
                    $userId = $request->id;
                    if($userId != null){
                        $user = User::find($userId);
                        if(!$user){
                           return $this->returnError('' , 'this user doesn`t exists');
                        }
                        $user->delete();
                       return $this->returnSuccessMessage('user removed successfuly');
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
    //function for delete donors
    public function deleteDonor(Request $request){
        $token = $request->header('auth-token');
            if($token){
                try{
                    $donorId = $request->id;
                    if($donorId != null){
                        $donor = Donor::find($donorId);
                        if(!$donor){
                           return $this->returnError('' , 'this donor doesn`t exists');
                        }
                        $donor->delete();
                       return $this->returnSuccessMessage('donor removed successfuly');
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
    // public function return online and last seen for user
    // public function returnUsers (){
    //     try {
    //         $users = User::all()->whereNotNull('last_seen')->orderBy('last_seen' , 'DESC');
    //         // if($users->isEmpty()){
    //         //     return $this->returnError('000' , "no users has last seen time");
    //         // }
    //         return $this->returnData('users' , $users);
    //     } catch(Exception $ex){
    //         return $this->returnError($ex->getCode(), $ex->getMessage());
    //     }
    // }

}
