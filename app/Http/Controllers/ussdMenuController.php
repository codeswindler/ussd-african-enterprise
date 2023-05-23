<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\eventRegistration;
use App\Http\Controllers\sendSMS\SmsAlertController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $currentTime = Carbon::now();

        $group1 = [
            '1:Embakasi East',
            '2:Embakasi West',
            '3:Embakasi Central',
            '4:Makadara',
            '5:Kamukunji',
            '6:Dagoretti South',
            '7:Langata',
            '8:Youth Alliance',
            '9:MORE'
        ];

        $group2 = [
            '10:Ruaraka',
            '11:Roysambu',
            '12:Embakasi North',
            '13:Embakasi South',
            '14:Northern Gate',
            '15:Women Alliance',
            '16:Dagoretti North',
            '17:Starehe',
            '18:MORE'
        ];

        $group3 = [
            '19:Kahawa West',
            '20:Eastern Gate',
            '21:Kasarani',
            '22:Mathare',
            '23:Kibra',
            '24:Westlands',
            '25:Western Gate'
        ];
        
        

        if ($lastInput == "76") {

            $response = "CON Welcome to Love Nairobi Festival Launch. Please select an option\n1.Register";
            return response($response)->header('Content-Type', 'text/plain');
        } elseif ($lastInput == "1") {


            $mobile = DB::table('event_registrations')->where('mobile', $msisdn)->where('status', '1')->first();

            if ($mobile) {
                $response = "END You are already registered";
                return response($response)->header('Content-Type', 'text/plain');
            } else {

                $mobile = DB::table('event_registrations')->where('mobile', $msisdn)->where('status', '0')->first();

                if ($mobile) {

                    if (!$mobile->Church_Name) {


                        $response = "CON Enter Name Of Church/Organization represented";
                        return response($response)->header('Content-Type', 'text/plain');
                    } else if (!$mobile->Sub_County) {


                        $response = "CON Choose a Zone:\n";
                        foreach ($group1 as $group1) {
                            $response .= $group1 . "\n";
                        }

                        return response($response)->header('Content-Type', 'text/plain');

                        if ($lastInput == '9') {
                            $response = "CON Choose a Zone:\n";
                            foreach ($group2 as $group2) {
                                $response .= $group2 . "\n";
                            }
                            
                            return response($response)->header('Content-Type', 'text/plain');
                        }elseif($lastInput == '18'){
        
                            $response = "CON Choose a Zone";
        
                            foreach ($group3 as $group3) {
                                $response .= $group3 . "\n";
                            }
        
                            return response($response)->header('Content-Type', 'text/plain');
        
                        }else{

                            $response = "CON Enter Full Name";
                            return response($response)->header('Content-Type', 'text/plain');
                        }
                    }

                    
                } else {

                    DB::table('event_registrations')->insertOrIgnore(['mobile' => $msisdn]);
                    $response = "CON Enter Full Name";
                    return response($response)->header('Content-Type', 'text/plain');
                }
            }
        } elseif ($lastInput != '') {


            $registration = DB::table('event_registrations')->where('mobile', $msisdn)->where('status', '0')->first();

            if (!$registration->name && !$registration->Church_Name && !$registration->Sub_County) {

                DB::table('event_registrations')->where('mobile', $msisdn)->where('status', '0')->update(['name' => $lastInput, 'created_at' => $currentTime]);

                $response = "CON Enter Name Of Church/Organization represented";

                return  response($response)->header('Content-Type', 'text/plain');
            } else if ($registration->name && !$registration->Church_Name && !$registration->Sub_County) {
                DB::table('event_registrations')->where('mobile', $msisdn)->where('status', '0')->update(['Church_Name' => $lastInput, 'created_at' => $currentTime]);

                $response = "CON Choose a Zone:\n";


                foreach ($group1 as $group1) {
                    $response .= $group1 . "\n";
                }

                return response($response)->header('Content-Type', 'text/plain');

               
            } else {

                if ($lastInput == '9') {
                    $response = "CON Choose a Zone:\n";
                    foreach ($group2 as $group2) {
                        $response .= $group2 . "\n";
                    }
                    
                    return response($response)->header('Content-Type', 'text/plain');
                }elseif($lastInput == '18'){

                    $response = "CON Choose a Zone";

                    foreach ($group3 as $group3) {
                        $response .= $group3 . "\n";
                    }

                    return response($response)->header('Content-Type', 'text/plain');

                }else{

                    DB::table('event_registrations')->where('mobile', $msisdn)->where('status', '0')->update(['Sub_County' => $lastInput, 'status' => '1', 'created_at' => $currentTime]);
                $sendSMS = new SmsAlertController();
                $resp = $sendSMS->sendSMS($msisdn);
    
                $response = " END Registration Successful";
                
                return response($response)->header('Content-Type', 'text/plain');

                }
            
                
                }

                
            }
        }
    }

