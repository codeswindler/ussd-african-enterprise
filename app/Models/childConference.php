<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class childConference extends Model {
    use HasFactory;

    protected $table = 'child_conference';
    protected $fillable = ['child_first_name', 'child_second_name', 'child_email', 'child_ministry','ministry_role','child_gender','child_phone_no'];

}
