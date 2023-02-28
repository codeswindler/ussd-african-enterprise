<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\eventRegistration;
use App\Http\Controllers\sendSMS\SmsAlertController;
use Illuminate\Support\Facades\DB;

class ussdMenuController extends Controller
{
    //

    public function index(Request $request)
    {

        $sessionID = $request->input('SESSIONID');
        $ussdCode = $request->input('USSDCODE');
        $msisdn = $request->input('MSISDN');
        $input = $request->input('INPUT');


        $inputArray = explode("*", $input);
        $lastInput = end($inputArray);

        if ($lastInput == "80") {

            $response = "CON Welcome to Love Nairobi Festival Launch. Please select an option\n1.Register";
            return response($response)->header('Content-Type', 'text/plain');
        } elseif ($lastInput == "1") {

          
            $mobile = DB::table('event_registrations')->where('mobile', $msisdn)->first();

            if ($mobile) {
                $response = "END You are already registered";
                return response($response)->header('Content-Type', 'text/plain');
            } else {
                DB::table('event_registrations')->insert(['mobile' => $msisdn]);
                
                $response = "CON Enter First Name";
                return response($response)->header('Content-Type', 'text/plain');
            }
        } elseif ($lastInput != ''){

            $registration = DB::table('event_registrations')->where('mobile', $msisdn)->first();
            
            if (!$registration->name){

                DB::table('event_registrations')->where('mobile', $msisdn)->update(['name' => $lastInput]);

            $response = "CON Enter Church/Organization name represented";

            return  response($response)->header('Content-Type', 'text/plain');

           }else if(!$registration->Church_Name){
            DB::table('event_registrations')->where('mobile', $msisdn)->update(['Church_Name' => $lastInput]);

        $response = "CON Enter Sub-County Name";

        return response($response)->header('Content-Type', 'text/plain');

            }else if($registration->Sub_County){

                DB::table('event_registrations')->where('mobile', $msisdn)->update(['Sub_County' => $lastInput]);
            $response = "Registration Succesful";

            return response($response)->header('Content-Type', 'text/plain');

            }else{
                $response = "END You are registered";
                $sendSMS = new SmsAlertController();
                $resp = $sendSMS->sendSMS($msisdn);

            return response($response)->header('Content-Type', 'text/plain');


            }
            

           
        }  
    }
}
