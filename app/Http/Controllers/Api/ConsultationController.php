<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultationAvailabity;
use App\Models\ConsultationAvailabityHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    //

    public function updateAvailability(Request $request) {

        $validator = Validator::make($request->all(), [
            'sunday'      => 'boolean',
            'sundayHours' => 'array',

            'monday'      => 'boolean',
            'mondayHours' => 'array',

            'tuesday'     => 'boolean',
            'tuesdayHours' => 'array',

            'wednesday'   => 'boolean',
            'wednesdayHours' => 'array',

            'thursday'    => 'boolean',
            'thursdayHours' => 'array',

            'friday'      => 'boolean',
            'fridayHours' => 'array',

            'saturday'    => 'boolean',
            'saturdayHours' => 'array',
        ]);
        $validator->setAttributeNames([
            'sunday' => 'Domingo',
            'sundayHours' => 'Horários de Domingo',

            'monday' => 'Segunda-feira',
            'mondayHours' => 'Horários de Segunda-feira',

            'tuesday' => 'Terça-feira',
            'tuesdayHours' => 'Horários de Terça-feira',

            'wednesday' => 'Quarta-feira',
            'wednesdayHours' => 'Horários de Quarta-feira',

            'thursday' => 'Quinta-feira',
            'thursdayHours' => 'Horários de Quinta-feira',

            'friday' => 'Sexta-feira',
            'fridayHours' => 'Horários de Sexta-feira',

            'saturday' => 'Sábado',
            'saturdayHours' => 'Horário de Sábado',
        ]);

        //Falha na autenticação
        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);

        $userID = $request->user()->id;
        //Cadastra os dias disponiveis
        $availability = ConsultationAvailabity::where('user_id', $userID)->first();
        if (!$availability) 
            $availability = ConsultationAvailabity::create([
                'user_id' => $userID,
            ]);
        
        $availability->update($request->except('user_id'));

        //Cadastra os horários disponiveis
        ConsultationAvailabityHour::where('consultation_availabity_id', $availability->id)->delete();
        
        if ($request->sunday) $this->createHours($availability->id, $request->sundayHours, 'sunday');
        if ($request->monday) $this->createHours($availability->id, $request->mondayHours, 'monday');
        if ($request->tuesday) $this->createHours($availability->id, $request->tuesdayHours, 'tuesday');
        if ($request->wednesday) $this->createHours($availability->id, $request->wednesdayHours, 'wednesday');
        if ($request->thursday) $this->createHours($availability->id, $request->thursdayHours, 'thursday');
        if ($request->friday) $this->createHours($availability->id, $request->fridayHours, 'friday');
        if ($request->saturday) $this->createHours($availability->id, $request->saturdayHours, 'saturday');

        return response()->json($availability);
    }

    private function createHours($availabilityID, $hours, $day) {
        foreach ($hours as $hour) {
            ConsultationAvailabityHour::create([
                'consultation_availabity_id' => $availabilityID,
                'day' => $day,
                'hour' => $hour,
            ]);
        }
    }   

    public function getAvailability(Request $request, $specialistID) {
        
        $availability = ConsultationAvailabity::where('user_id', $specialistID)->firstOrFail();
        return response()->json($availability);
    }
}
