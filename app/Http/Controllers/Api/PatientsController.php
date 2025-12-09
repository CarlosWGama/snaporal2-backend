<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientsController extends Controller {
    //

    /**
     * Cadastra um novo paciente
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'nullable',
            'birth_date' => 'nullable|date',
            'gender' => 'required',
        ]);

        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);
        
        $data = $request->all();
        $data['created_by_user_id'] = $request->user()->id;
        $data['updated_by_user_id'] = $request->user()->id;

        $patient = Patient::create($data);

        return response()->json($patient, 201);
    }

    /**
     * Busca um paciente pelo ID
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $id) {
        $patient = Patient::findOrFail($id);
        return response()->json($patient, 200);
    }

    /**
     * Lista todos os pacientes
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request) {
        $patientModel = Patient::query();
        $page = $request->page ?? 1;
        $pageSize = 10;

        if ($request->name) $patientModel = $patientModel->whereRaw('lower(name) like ?', ['%' . strtolower($request->name) . '%']);
        if ($request->gender) $patientModel = $patientModel->where('gender', $request->gender);

        $patients = $patientModel->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $patients->items(),
            'total' => $patients->total(),
            'currentFirst'  => $patients->firstItem(),
            'currentLast'   => $patients->lastItem(),
            'firstPage'     => $patients->onFirstPage(),
            'lastPage'      => $patients->onLastPage(),
        ]);
    }

    /**
     * Atualiza
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'nullable',
            'birth_date' => 'nullable|date',
            'gender' => 'required',
        ]);

        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);
        
        $data = $request->except('created_by_user_id');
        $data['updated_by_user_id'] = $request->user()->id;

        $patient = Patient::findOrFail($id);
        $patient->fill($data);
        $patient->save();

        return response()->json($patient, 200);
    }

    /**
     * Remove um paciente
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id) {
        $patient = Patient::findOrFail($id);
        
        if (!$request->user()->tokenCan('admin') && $request->user()->id != $patient->created_by_user_id) 
            return response()->json('Apenas usuário administradores ou criadores do paciente podem removê-lo', 401);
        
        $patient->delete();
        return response()->json($patient, 200);
    }

    /**
     * Retorna a lista de evoluções de um paciente
     */
    public function listProgresses(Request $request, $patientID) {
        $progresses = PatientProgress::where('patient_id', $patientID)->orderBy('date', 'desc')->get();
        return response()->json($progresses, 200);
    }

    /**
     * Retorna a evolução de um paciente
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProgress(Request $request, $patientID, $id) {
        $progress = PatientProgress::findOrFail($id);
        return response()->json($progress, 200);
    }

    /**
     * Cadastra uma evolução de um paciente
     */
    public function createProgress(Request $request, $patientID) {
        $validador = Validator::make($request->all(), [
            'description' => 'required',
            'date' => 'date',
        ]);

        if ($validador->fails()) return response()->json($validador->errors()->all(), 400);
        
        $data = $request->all();
        $data['patient_id'] = $patientID;
        $data['created_by_user_id'] = $request->user()->id;
        $data['updated_by_user_id'] = $request->user()->id;

        $progress = PatientProgress::create($data);
        return response()->json($progress, 201);
    }

    /**
     * Atualiza uma evolução de um paciente
     */
    public function updateProgress(Request $request, $patientID, $id) {
        $validador = Validator::make($request->all(), [
            'description' => 'required',
            'date' => 'date',
        ]);

        if ($validador->fails()) return response()->json($validador->errors()->all(), 400);
        
        $data = $request->except(['patient_id', 'created_by_user_id']);
        $data['updated_by_user_id'] = $request->user()->id;

        $progress = PatientProgress::findOrFail($id);
        $progress->fill($data);
        $progress->save();
        return response()->json($progress, 200);
    }

    /**
     * Remove uma evolução de um paciente
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProgress(Request $request, $patientID, $id) {
        $progress = PatientProgress::findOrFail($id);
        $progress->delete();
        return response()->json($progress, 200);
    }
}
