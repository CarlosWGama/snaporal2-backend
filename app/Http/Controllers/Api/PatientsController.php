<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
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

        if ($request->name) $userModel = $patientModel->whereRaw('lower(name) like ?', ['%' . strtolower($request->name) . '%']);

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
        
        $data = $request->all();
        unset($data['created_by_user_id']);
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
    public function destroy(Request $request, $id) {
        $patient = Patient::findOrFail($id);
        
        if (!$request->user()->tokenCan('admin') && $request->user()->id != $patient->created_by_user_id) 
            return response()->json('Apenas usuário administradores ou criadores do paciente podem removê-lo', 401);
        
        $patient->delete();
        return response()->json($patient, 200);
    }
}
