<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class eventRegistration extends Model


{
    use HasFactory;

    protected $table = 'event_registrations';
    protected $fillable = ['name','Sub_County','Church_Name','mobile'];


}
