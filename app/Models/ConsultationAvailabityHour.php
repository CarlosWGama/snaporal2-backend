<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationAvailabityHour extends Model
{
    //
    protected $table = 'consultation_availabity_hours';

    protected $fillable = [
        'consultation_availabity_id',
        'day',
        'hour',
    ];
}
