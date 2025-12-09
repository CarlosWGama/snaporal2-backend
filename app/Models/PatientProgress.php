<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientProgress extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'description',
        'date',
        'patient_id',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $with = ['createdByUser', 'updatedByUser'];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function createdByUser() {
        return $this->belongsTo(User::class);
    }

    public function updatedByUser() {
        return $this->belongsTo(User::class);
    }
}
