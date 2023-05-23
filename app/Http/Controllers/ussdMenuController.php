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

        $subZones = [
            'Embakasi East',
            'Embakasi West',
            'Embakasi Central',
            'Makadara',
            'Kamukunji',
            'Dagoretti South',
            'Langata',
            'Youth Alliance',
            'Ruaraka',
            'Roysambu',
            // 'Embakasi North',
            // 'Embakasi South',
            // 'Northern Gate',
            // 'Women Alliance',
            // 'Dagoretti North',
            // 'Starehe',
            // 'Kahawa West',
            // 'Eastern Gate',
            // 'Kasarani',
            // 'Mathare',
            // 'Kibra',
            // 'Westlands',
            // 'Western Gate'
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

                        for ($i  = 0; $i < count($subZones); $i++) {

                            $response .= $i . ":" . $subZones[$i] . "\n";
                        }



                        return response($response)->header('Content-Type', 'text/plain');
                    }

                    $response = "CON Enter Full Name";
                    return response($response)->header('Content-Type', 'text/plain');
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

                for ($i  = 0; $i < count($subZones); $i++) {

                    $response .= $i . ":" . $subZones[$i] . "\n";
                }


                return response($response)->header('Content-Type', 'text/plain');
            } else {

                if (is_numeric($lastInput) && isset($subZones[$lastInput])) {
                    $lastInput = $subZones[$lastInput];
                }
                DB::table('event_registrations')->where('mobile', $msisdn)->where('status', '0')->update(['Sub_County' => $lastInput, 'status' => '1', 'created_at' => $currentTime]);
                $sendSMS = new SmsAlertController();
                $resp = $sendSMS->sendSMS($msisdn);
    
                $response = " END Registration Successful";
                
                return response($response)->header('Content-Type', 'text/plain');
            }
        }
    }
}
