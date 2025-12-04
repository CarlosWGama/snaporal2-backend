<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationAvailabity;
use App\Models\ConsultationAvailabityHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    /**
     * Atualiza a disponibilidade de um especialista
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Cadastra os horários disponiveis
     * @param int $availabilityID
     * @param array $hours
     * @param string $day
     * @return void
     */
    private function createHours($availabilityID, $hours, $day) {
        foreach ($hours as $hour) {
            ConsultationAvailabityHour::create([
                'consultation_availabity_id' => $availabilityID,
                'day' => $day,
                'hour' => $hour,
            ]);
        }
    }   

    /**
     * Busca a disponibilidade de um especialista
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailability(Request $request, $specialistID) {
        
        $availability = ConsultationAvailabity::where('user_id', $specialistID)->firstOrFail();
        return response()->json($availability);
    }

    /**
     * Cadastra uma consulta
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'professional_id' => 'required',
            'specialist_id' => 'required',
            'date' => 'required',
            'hour' => 'required',
        ]);
        $validator->setAttributeNames([
            'professional_id' => 'Profissional',
            'specialist_id' => 'Especialista',
            'date' => 'Data',
            'hour' => 'Hora',
        ]);

        //Falha na autenticação
        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);

        $consultation = Consultation::create($request->all());
        return response()->json($consultation, 201);
    }

    /**
     * Atualiza o status de uma consulta
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $consultationID) {
        $validator = Validator::make($request->all(), [
            'status'          => 'required',
        ]);
        $validator->setAttributeNames([
            'status'          => 'Status',
        ]);

        //Falha na autenticação
        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);

        
        if ($request->user()->admin)
            $consultation = Consultation::findOrFail($consultationID);
        else {
            $userID =  $request->user()->id;
            $consultation = Consultation::where('id', $consultationID)
                                            ->where('specialist_id', $userID)
                                            ->orWhere('professional_id', $userID)
                                            ->firstOrFail();
        }
        
        $consultation->update($request->only('status'));
        return response()->json($consultation, 200);
    }

    /**
     * Deleta uma consulta
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $consultationID) {
        if ($request->user()->admin)
            $consultation = Consultation::findOrFail($consultationID);
        else {
            $userID =  $request->user()->id;
            $consultation = Consultation::where('id', $consultationID)
                                            ->where('specialist_id', $userID)
                                            ->orWhere('professional_id', $userID)
                                            ->firstOrFail();
        }
        $consultation->delete();
    
        return response()->json("Consulta deletada com sucesso", 200);
    }

    /**
     * Lista as consultas
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request) {
        $consultationsModel = Consultation::query();
        $mobile = $request->mobile ?? false; //SE está vindo do mobile ou do gerenciador
        $pageSize = 5;
        $userID = $request->user()->id;
        
        if ($mobile || !$request->user()->admin) {
            $consultationsModel = $consultationsModel->where('specialist_id', $userID)
                                                        ->orWhere('professional_id', $userID);
        }

        $consultations = $consultationsModel->orderBy('date', 'desc')->orderBy('hour','desc')->paginate($pageSize);

        return response()->json([
            'items'         => $consultations->items(),
            'total'         => $consultations->total(),
            'currentFirst'  => $consultations->firstItem(),
            'currentLast'   => $consultations->lastItem(),
            'completed'     => $consultations->onLastPage(),
        ]);
    }

    /**
     * Busca uma consulta
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $consultationID) {
        if ($request->user()->admin)
            $consultation = Consultation::findOrFail($consultationID);
        else {
            $userID =  $request->user()->id;
            $consultation = Consultation::where('id', $consultationID)
                                            ->where('specialist_id', $userID)
                                            ->orWhere('professional_id', $userID)
                                            ->firstOrFail();
        }
        return response()->json($consultation);
    }
}
