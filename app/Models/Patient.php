<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'birth_date',
        'gender',
        'created_by_user_id',
        'updated_by_user_id'
    ];

    protected $with = ['createdByUser', 'updatedByUser'];

    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedByUser() {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
