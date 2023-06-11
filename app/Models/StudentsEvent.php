<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentsEvent extends Model {
    use HasFactory;

    protected $table = 'students_events';
    protected $fillable = ['firstname', 'surname', 'whatsapp', 'receive_updates'];

}
