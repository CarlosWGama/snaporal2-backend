<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'professional_id',
        'specialist_id',
        'date',
        'hour',
        'status',
    ];

    protected $with = ['professional', 'specialist'];

    public function professional() {
        return $this->belongsTo(User::class);
    }

    public function specialist() {
        return $this->belongsTo(User::class);
    }
}
