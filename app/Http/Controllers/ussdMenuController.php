<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class ussdMenuController extends Controller {

    private int $level;
    private string $sessionId;

    private array $group1 = [
        '1:Embakasi East',
        '2:Embakasi West',
        '3:Embakasi Central',
        '4:Makadara',
        '5:Kamukunji',
        '6:Dagoretti South',
        '7:Langata',
        '8:Youth Alliance',
        '9:MORE',
    ];

    private array $group2 = [
        '10:Ruaraka',
        '11:Roysambu',
        '12:Embakasi North',
        '13:Embakasi South',
        '14:Northern Gate',
        '15:Women Alliance',
        '16:Dagoretti North',
        '17:Starehe',
        '18:MORE',
    ];

    private array $group3 = [
        '19:Kahawa West',
        '20:Eastern Gate',
        '21:Kasarani',
        '22:Mathare',
        '23:Kibra',
        '24:Westlands',
        '25:Western Gate',
    ];


    public function index(Request $request
    ) : Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory {
        $this->sessionId = $request->input('SESSIONID');
        //$ussdCode = $request->input('USSDCODE');
        $msisdn = $request->input('MSISDN');
        $input = $request->input('INPUT');


        $inputArray = explode("*", $input);
        $lastInput = end($inputArray);
        // check if this is a new session
        $isNewSession = Redis::exists($this->sessionId);

        // level is how keep track of ussd sessions
        $this->level = Screen::INIT;
        if (!$isNewSession) {
            Redis::set($this->sessionId, Screen::INIT); // initialize session
        } else {
            $this->level = (int)Redis::get($this->sessionId); //fetch saved session
        }
        $result = match ($this->level) {
            Screen::INIT, Screen::WELCOME => $this->initScreen(),
            Screen::REGISTER => $this->registerScreen($lastInput, $msisdn),
            Screen::FULL_NAME => $this->fullNameScreen($lastInput),
            Screen::CHURCH_NAME => $this->churchOrgScreen($lastInput),
            Screen::ZONE_ONE => $this->zoneOneScreen($input),
            Screen::ZONE_TWO => $this->zoneTwoScreen($input),
            Screen::ZONE_THREE => $this->zoneThreeScreen($input),
            default => "END menu is not set"
        };

        Redis::set($this->sessionId, $this->level); // save the next level
        return response($result)->header('Content-Type', 'text/plain');
    }

    // this ignores the duplicate first request
    private function initScreen() : string {
        $this->level = Screen::WELCOME; // go to the next screen
        return "CON Welcome to Love Nairobi Festival Launch. Please select an option to Register" .
            "\n1.LNF Pastors & Leaders Enrichment conference" .
            "\n2.Kenya Students Christian Fellowship Nairobi County";
    }

    private function welcomeScreen() : string {
        $this->level = Screen::REGISTER; // go to the next screen
        return "CON Welcome to Love Nairobi Festival Launch. Please select an option to Register" .
            "\n1.LNF Pastors & Leaders Enrichment conference" .
            "\n2.Kenya Students Christian Fellowship Nairobi County";
    }

    private function registerScreen($input, $msisdn) : string {
        if ($input == 1) {
            $isUserRegistered = DB::table('event_registrations')
                ->where('mobile', $msisdn)
                ->where('status', '1')
                ->first();
            if ($isUserRegistered) {
                return "END You are already registered";
            }
            Redis::set("$this->sessionId:msisdn", $input);
            $this->level = Screen::FULL_NAME;
            return "CON Enter Full Name";
        }

        if ($input == 2) { //kenya students
            // todo implement me
            return "END hello student christian";
        }

        return "END Invalid option";
    }

    private function fullNameScreen($input) : string {
        Redis::set("$this->sessionId:name", $input);
        $this->level = Screen::CHURCH_NAME;
        return "CON Enter Name Of Church/Organization represented";
    }

    private function churchOrgScreen($input) : string {
        Redis::set("$this->sessionId:church", $input);
        $this->level = Screen::ZONE_ONE;

        $response = "CON Choose a Zone:\n";
        foreach ($this->group1 as $group) {
            $response .= $group . "\n";
        }
        return $response;
    }

    private function zoneOneScreen(string $input) : string {
        if ($input == "9") {
            $this->level = Screen::ZONE_TWO;
            $response = "CON Choose a Zone:\n";
            foreach ($this->group2 as $group) {
                $response .= $group . "\n";
            }
            return $response;
        }
        Redis::set("$this->sessionId:zone", $input);

        return $this->saveEvent();
    }

    private function zoneTwoScreen(string $input) : string {
        if ($input == "18") {
            $this->level = Screen::ZONE_THREE;
            $response = "CON Choose a Zone:\n";
            foreach ($this->group3 as $group) {
                $response .= $group . "\n";
            }
            return $response;
        }
        Redis::set("$this->sessionId:zone", $input);

        return $this->saveEvent();
    }

    private function zoneThreeScreen(string $input) : string {
        Redis::set("$this->sessionId:zone", $input);
        return $this->saveEvent();
    }

    private function saveEvent() : string {
        $msisdn = Redis::get("$this->sessionId:msisdn");
        $name = Redis::get("$this->sessionId:name");
        $church = Redis::get("$this->sessionId:church");
        $zone = Redis::get("$this->sessionId:zone");

        DB::table("event_registrations")->insertOrIgnore([
            "mobile"      => $msisdn,
            "name"        => $name,
            "Sub_County"  => $zone,
            "Church_Name" => $church,
            "status"      => '1',
            "created_at"  => Carbon::now(),
        ]);

        return "END Registration Successful";
    }
}


