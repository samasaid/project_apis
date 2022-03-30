<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\users\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\GeneralTrait;
use Exception;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function storeContactForm(Request $request){
        try{
            // validation
            $rules = [
                'name' => 'required|string',
                'email' => 'required|email',
                'subject' => 'required',
                'content' => 'required',
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

            $input = $request->all();

            Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'content' => $request->content,
            ]);

            //  Send mail to admin
            Mail::send('contactMail', array(
                'name' => $input['name'],
                'email' => $input['email'],
                'subject' => $input['subject'],
                'message' => $input['message'],
            ), function($message) use ($request){
                $message->from($request->email);
                $message->to('healthhistory2022@gmail.com', 'Health History')->subject($request->get('subject'));
            });
            return $this->returnSuccessMessage('your message successfully sent!');
        }catch(Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
