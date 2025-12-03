<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationAvailabity extends Model
{
    //
    protected $table = 'consultation_availabities';

    protected $fillable = [
        'user_id',
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
    ];

    protected $appends = ['sundayHours', 'mondayHours', 'tuesdayHours', 'wednesdayHours', 'thursdayHours', 'fridayHours', 'saturdayHours'];

    public function getsundayHoursAttribute() {
        return $this->getHours('sunday');
    }

    public function getmondayHoursAttribute() {
        return $this->getHours('monday');
    }

    public function gettuesdayHoursAttribute() {
        return $this->getHours('tuesday');
    }

    public function getwednesdayHoursAttribute() {
        return $this->getHours('wednesday');
    }

    public function getthursdayHoursAttribute() {
        return $this->getHours('thursday');
    }

    public function getfridayHoursAttribute() {
        return $this->getHours('friday');
    }

    public function getsaturdayHoursAttribute() {
        return $this->getHours('saturday');
    }

    private function getHours($day) {
        $hours = [];
        $data = ConsultationAvailabityHour::select('hour')->where('consultation_availabity_id', $this->id)->where('day', $day)->get();
        foreach ($data as $hour) {
            $hours[] = $hour->hour;
        }
        return $hours;
    }

    protected function casts(): array
    {
        return [
            'sunday' => 'boolean',
            'monday' => 'boolean',
            'tuesday' => 'boolean',
            'wednesday' => 'boolean',
            'thursday' => 'boolean',
            'friday' => 'boolean',
            'saturday' => 'boolean',
        ];
    }
}
