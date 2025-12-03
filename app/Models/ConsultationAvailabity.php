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

    protected $appends = ['SundayHours', 'MondayHours', 'TuesdayHours', 'WednesdayHours', 'ThursdayHours', 'FridayHours', 'SaturdayHours'];

    public function getSundayHoursAttribute() {
        return $this->getHours('sunday');
    }

    public function getMondayHoursAttribute() {
        return $this->getHours('monday');
    }

    public function getTuesdayHoursAttribute() {
        return $this->getHours('tuesday');
    }

    public function getWednesdayHoursAttribute() {
        return $this->getHours('wednesday');
    }

    public function getThursdayHoursAttribute() {
        return $this->getHours('thursday');
    }

    public function getFridayHoursAttribute() {
        return $this->getHours('friday');
    }

    public function getSaturdayHoursAttribute() {
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
}
