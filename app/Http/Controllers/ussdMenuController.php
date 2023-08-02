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
            Screen::INIT => $this->initScreen(),
            Screen::WELCOME => $this->welcomeScreen(),
            Screen::REGISTER => $this->registerScreen($lastInput, $msisdn),
            Screen::FULL_NAME => $this->fullNameScreen($lastInput),
            Screen::CHURCH_NAME => $this->churchOrgScreen($lastInput),
            Screen::ZONE_ONE => $this->zoneOneScreen($lastInput),
            Screen::ZONE_TWO => $this->zoneTwoScreen($lastInput),
            Screen::ZONE_THREE => $this->zoneThreeScreen($lastInput),
            Screen::STUDENT_FIRST_NAME => $this->studentFirstName($lastInput),
            Screen::STUDENT_SURNAME => $this->studentSurname($lastInput),
            Screen::STUDENT_WHATSAPP_NO => $this->studentWhatsappNo($lastInput),
            Screen::STUDENT_UPDATES_CONSENT => $this->studentUpdatesConsent($lastInput),
            Screen::CHILD_FIRST_NAME => $this->childFirstName($lastInput),
            Screen::CHILD_SECOND_NAME => $this->childSecondName($lastInput),
            Screen::CHILD_EMAIL => $this->childEmail($lastInput),
            Screen::CHILD_MINISTRY => $this->childMinistry($lastInput),
            Screen::MINISTRY_ROLE => $this->ministryRole($lastInput),
            Screen::CHILD_GENDER => $this->childGender($lastInput),
            Screen::CHILD_PHONE_NO => $this->childPhoneNo($lastInput),
            Screen::FISRT_NAME => $this->firstName($lastInput),
            Screen::MIDDLE_NAME => $this->middleName($lastInput),
            Screen::SUR_NAME => $this->surName($lastInput),
            Screen::WHATSAPP_NUMBER => $this->whatsappNumber($lastInput),
            Screen::ADDITIONAL_NUMBER => $this->additionalNumber($lastInput),
            Screen::FEST_EMAIL => $this->festEmail($lastInput),
            Screen::SUB_COUNTY => $this->subCounty($lastInput),
            Screen::WARD => $this->Ward($lastInput),
            Screen::ESTATE => $this->Estate($lastInput),
            Screen::DATE_TRAINING => $this->dateTraining($lastInput),
            default => "END menu is not set"
        };

        Redis::set($this->sessionId, $this->level); // save the next level
        return response($result)->header('Content-Type', 'text/plain');
    }

    // this ignores the duplicate first request
    private function initScreen() : string {
        $this->level = Screen::WELCOME; // go to the next screen
        return "CON Welcome to Love Nairobi Festival Launch. Select an option" .
        "\n1.LNF Pastors & Leaders Enrichment conference" .
        "\n2.Kenya Students Christian Fellowship Nairobi County" .
        "\n3.Love Nairobi Festival Counsellor Registration" .
        "\n4.Children Minister's Conference";
}

    private function welcomeScreen() : string {
        $this->level = Screen::REGISTER; // go to the next screen
        return "CON Welcome to Love Nairobi Festival Launch. Select an option" .
            "\n1.LNF Pastors & Leaders Enrichment conference" .
            "\n2.Kenya Students Christian Fellowship Nairobi County"
            "\n3.Love Nairobi Festival Counsellor Registration"
            "\n4.Children Minister's Conference";
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
            Redis::set("$this->sessionId:msisdn", $msisdn);
            $this->level = Screen::FULL_NAME;
            return "CON Enter Full Name";
        }

        if ($input == 2) { //kenya students
            $this->level = Screen::STUDENT_FIRST_NAME;
            return "CON Enter your first name";
        }
        if ($input == 3) { //Nairobi fest
            $this->level = Screen::FIRST_NAME;
            return "CON Enter your first name";
        }
        if ($input == 4) { //children conference
            $this->level = Screen::CHILD_FIRST_NAME;
            return "CON Enter your first name";

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


    private function studentFirstName($input) : string {
        Redis::set("$this->sessionId:firstname", $input);
        $this->level = Screen::STUDENT_SURNAME;

        return "CON Enter your surname\n";
    }

    private function studentSurname($input) : string {
        Redis::set("$this->sessionId:surname", $input);
        $this->level = Screen::STUDENT_WHATSAPP_NO;

        return "CON Enter your Whatsapp number\n";
    }

    private function studentWhatsappNo($input) : string {
        Redis::set("$this->sessionId:whatsapp", $input);
        $this->level = Screen::STUDENT_UPDATES_CONSENT;

        return "CON Would you like to receive updates\n1. Yes\n2. No";
    }

    private function studentUpdatesConsent($input) : string {
        Redis::set("$this->sessionId:consent", $input);

        $hasConsented = true;
        if ($input == "2") {
            $hasConsented = false;
        }

        $firstname = Redis::get("$this->sessionId:firstname");
        $surname = Redis::get("$this->sessionId:surname");
        $whatsapp = Redis::get("$this->sessionId:whatsapp");

        DB::table("students_events")->insertOrIgnore([
            "firstname"       => $firstname,
            "surname"         => $surname,
            "whatsapp"        => $whatsapp,
            "receive_updates" => $hasConsented ? 1 : 0,
            "created_at"      => Carbon::now(),
        ]);

        return "END Thanks for registering";
    }

    private function firstName($input) : string {
        Redis::set("$this->sessionId:first_name", $input);
        $this->level = Screen::MIDDLE_NAME;

        return "CON Enter your middle name\n";
    }

    private function middleName($input) : string {
        Redis::set("$this->sessionId:middle_name", $input);
        $this->level = Screen::SUR_NAME;

        return "CON Enter your surname\n";
    }

    private function surName($input) : string {
        Redis::set("$this->sessionId:sur_name", $input);
        $this->level = Screen::WHATSAPP_NUMBER;

        return "CON Enter your whatsapp number. No";
    }

    private function whatsappNumber($input) : string {
        Redis::set("$this->sessionId:whatsapp_number", $input);
        $this->level = Screen::ADDITIONAL_NUMBER;

        return "CON Enter your additional No";
    }
    
    
    private function additionalNumber($input) : string {
        Redis::set("$this->sessionId:additional_number", $input);
        $this->level = Screen::FEST_EMAIL;

        return "CON Enter your email. No";
    }
    
    private function festEmail($input) : string {
        Redis::set("$this->sessionId:fest_email", $input);
        $this->level = Screen::SUB_COUNTY;

        return "CON Enter your subcounty";
    }
    
    private function subCounty($input) : string {
        Redis::set("$this->sessionId:subcounty", $input);
        $this->level = Screen::WARD;

        return "CON Enter your Ward ";
    }
    
    private function Ward($input) : string {
        Redis::set("$this->sessionId:ward", $input);
        $this->level = Screen::ESTATE;

        return "CON Enter your estate";
    }
    
    private function Estate($input) : string {
        Redis::set("$this->sessionId:estate", $input);
        $this->level = Screen::DATE_TRAINING;

        return "CON Enter your desired Training Date";
    }

        $firstName = Redis::get("$this->sessionId:first_name");
        $middlename = Redis::get("$this->sessionId:middle_name");
        $surName = Redis::get("$this->sessionId:sur_name");
        $whatsApp = Redis::get("$this->sessionId:whatsapp_number");
        $additionalNo = Redis::get("$this->sessionId:additional_number");
        $email = Redis::get("$this->sessionId:fest_email");
        $subcounty = Redis::get("$this->sessionId:subcounty");
        $estate = Redis::get("$this->sessionId:estate");

        DB::table("fest_event")->insertOrIgnore([
            "first_name"             => $firstName,
            "middle_name"            => $middlename,
            "sur_name"               => $surName,
            "whatsapp_number"        => $whatsApp,
            "additonal_number"       => $additionalNo
            "fest_email"             => $email
            "subcounty"              => $subcounty 
            "ward"                   => $ward
            "estate"                 => $estate
            "created_at"             => Carbon::now(),
        ]);

        return "END Thanks for registering";
    }

    private function childFirstName($input) : string {
        Redis::set("$this->sessionId:child_first_name", $input);
        $this->level = Screen::CHILD_SECOND_NAME;

        return "CON Enter your second name\n";
    }

    private function childSecondName($input) : string {
        Redis::set("$this->sessionId:child_second_name", $input);
        $this->level = Screen::CHILD_EMAIL;

        return "CON Enter your email\n";
    }

    private function childEmail($input) : string {
        Redis::set("$this->sessionId:child_email", $input);
        $this->level = Screen::CHILD_MINISTRY;

        return "CON Enter your ministry, organization or church";
    }

    private function childMinistry($input) : string {
        Redis::set("$this->sessionId:child_ministry", $input);
        $this->level = Screen::MINISTRY_ROLE;

        return "CON Enter your role or position";
    }
    
    
    private function ministryRole($input) : string {
        Redis::set("$this->sessionId:ministry_role", $input);
        $this->level = Screen::CHILD_GENDER;

        return "CON What is your gender?";
    }
    
    private function childGender($input) : string {
        Redis::set("$this->sessionId:child_gender", $input);
        $this->level = Screen::CHILD_PHONE_NO;

        return "CON Enter your phone No";
    }
    
        $childFirstName = Redis::get("$this->sessionId:child_first_name");
        $childSecondName = Redis::get("$this->sessionId:child_second_name");
        $childEmail = Redis::get("$this->sessionId:child_email");
        $childMinistry = Redis::get("$this->sessionId:child_ministry");
        $ministryRole = Redis::get("$this->sessionId:ministry_role");
        $childGender = Redis::get("$this->sessionId:child_gender");
        $childPhoneNo = Redis::get("$this->sessionId:child_phone_no");

        DB::table("fest_event")->insertOrIgnore([
            "child_first_name"        => $childFirstName,
            "child_second_name"       => $childSecondName,
            "child_email"             => $childEmail
            "child_ministry"          => $childMinistry 
            "ministry_role"           => $ministryRole
            "child_gender"            => $childGender
            "child_phone_no"          =>$childPhoneNo
            "created_at"              => Carbon::now(),
        ]);

        return "END Thanks for registering";
    }

}


