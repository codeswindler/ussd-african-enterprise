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

        switch($network){

            case 'Safaricom':
                return $this->send($data,'xxxxxx');
                break;
            case 'Airtel':
                return $this->send($data,'xxxxx');
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
                    'apikey' => 'xxxx',
                    'partnerID' => 'xxxx',
                    'mobile' => $data,
                    'message' => 'Thank you for registering xxxx',
                    'shortcode' => $senderID,
                    'pass_type' => 'plain',
                ],
            ]
        );
    
        return $response;
        }

           
    
    }
    

    }



