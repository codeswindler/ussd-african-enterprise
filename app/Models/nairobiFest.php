<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nairobiFest extends Model {
    use HasFactory;

    protected $table = 'nairobi_fest';
    protected $fillable = ['first_name','middle_name','sur_name', 'whatsapp_number','additional_number','fest_email','subcounty','ward','estate'];

}
