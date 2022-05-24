<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\users\Donor;
use App\Traits\GeneralTrait;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;

class DonorsController extends Controller
{
    use GeneralTrait;
    public function addDonor(Request $request){
        try{
        // validation
            $rules = [
                "name" => "required|regex:/^[\pL\s\-]+$/u",
                'national_id'=> "required|regex:/^[0-9]+$/|unique:donors,national_id|max:14",
                'mobile'=>"required|regex:/^[0-9]+$/|unique:donors,mobile|max:11",
                'address'=>"required|exists:provinces,name|string",
                'blood_type'=>"required|string|in:A+,O+,B+,AB+,A-,O-,B-,AB-",

            ];
            $messages = [
                "required"=>"this filed is Required",
                "name.regex"=>"this filed must be letters",
                "national_id.regex"=>"this filed must be numeric",
                "mobile.regex"=>"this filed must be numeric",
                "in"=>"this value is not in the list",
                "national_id.unique"=>"the national number has already been added",
                "mobile.unique"=>"the mobile number has already been added",
                "national_id.max"=>"the national number must be 14 characters long",
            ];

            $validator = Validator::make($request->all(), $rules , $messages);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            //register
            $user = Donor::create([
                'name'=>$request->name,
                'national_id'=>$request->national_id,
                'mobile'=>$request->mobile,
                'address'=>$request->address,
                'blood_type'=>$request->blood_type,
            ]);
            //return success message
            $msg = "You have been added as a donor and those who search for your blood type can contact you";
            return $this->returnSuccessMessage($msg);
            }catch(Exception $ex){
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
    }
    public function getAllDonors(){
        try {
            $donors = Donor::all();
            if($donors->isEmpty()){
                return  $this -> returnError('','Sorry, there are no donors');
            }
            return $this->returnData('Donors' , $donors);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    public function searchAboutDonorByProvincesAndBloodType(Request $request){
        try {
            $donorsBYAddressAndBloodType = Donor::where('address' , '=' ,$request->address)
            ->Where('blood_type' , '=' ,$request->blood_type)->get();
            if($donorsBYAddressAndBloodType->isEmpty()){
                return  $this -> returnError('','Sorry, there are no donors');
            }
            return $this->returnData('Donors' , $donorsBYAddressAndBloodType);
        } catch (Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
