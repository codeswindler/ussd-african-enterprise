<?php

namespace App\Http\Controllers\sendSMS;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Nixon\MobileNumber\MobileNumber;



class SmsAlertController extends Controller
{
   public function sendSMS($data){


       $network = MobileNumber::getNetwork($data);
        $sender = env('APP_SENDERID');

        switch($network){

            case 'Safaricom':
                return $this->send($data,$sender);
                break;
            case 'Airtel':
                return $this->send($data,$sender);
                break;

            default:
                return $this->send($data, 'null');
                break;
        }
 
    
    }


    public function send($data,$senderID){

      
        if($senderID == null){
            return;
        }else{
            $client = new Client();
            $response = $client->post(
            'https://quicksms.advantasms.com/api/services/sendsms/',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [

                    'apikey' => env('APP_APIKEY'),
                    'partnerID' => env('APP_ID'),
                    'mobile' => $data,
                    'message' => 'Thank you for registering',

                    'shortcode' => $senderID,
                    'pass_type' => 'plain',
                ],
            ]
        );
    
        return $response;
        }

           
    
    }
    

    }



