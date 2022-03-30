<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    use GeneralTrait;
    public function sendEmail(Request $request){
        try{
            // validation
            $rules = [
                'email' => 'required|email',
                'message' => 'required',
                'name' => 'required|string',
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
            $data = [
            'message' => $request->subject,
            'name' => $request->name,
            'email' => $request->email,
            ];

            Mail::send('email-template', $data, function($message) use ($data) {
            $message->to($data['email'])
            ->subject($data['message']);
            });
            return $this->returnSuccessMessage('Email successfully sent!');
        }catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
         }
    }
}
