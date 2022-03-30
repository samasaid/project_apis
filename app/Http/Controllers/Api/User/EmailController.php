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
    //web site send message to user
    public function sendEmail(Request $request){
        try{
            // validation
            $rules = [
                'email' => 'required|email',
                'content' => 'required',
                'subject' => 'required',
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
            'subject' => $request->subject,
            'content' => $request->content,
            'name' => $request->name,
            'email' => $request->email,
            ];

            Mail::send('email-template', $data, function($message) use ($data) {
            $message->to($data['email'])
            ->subject($data['subject']);
            });
            return $this->returnSuccessMessage('Email successfully sent!');
        }catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
         }
    }
}
