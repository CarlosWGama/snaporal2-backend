<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RecoverPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class UsersController extends Controller
{
    /**
     * Realiza o login do usuário
     **/
    public function login(Request $request) {
        if (Auth::attempt($request->only(['email', 'password']))) {
            $user = Auth::user();
            
            //Permissões
            $permissoes = [];
            if ($user->admin) $permissoes[] = 'admin';
            if ($user->nivel_id == 1) $permissoes[] = 'profissional';
            else $permissoes[] = 'especialista';
            
            $token = $user->createToken('token', $permissoes, now()->addWeek(4))->plainTextToken;
            return response()->json(['token' => $token, 'user' => $user], 200);
        }

        return response()->json('Login ou senha inválidos', 404);
    }

    /**
     * Realiza o logout do usuário
     **/
    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json('Deslogado', 201);
    }

    /**
     * Retorna os dados do usuário logado
     */
    public function profile(Request $request) {
        return response()->json($request->user(), 200);
    }

    /**
     * Cria uma nova conta de usuário
     **/
    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'role_id'  => 'required|numeric'
        ]);
 
        //Falha na autenticação
        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);
        
        //Cadastrar
        $dados = $request->only(['name', 'email', 'password', 'role_id', 'admin']);
        if (!$request->user() || !$request->user()->tokenCan('admin')) {
            $dados['admin'] = false;
            $dados['role_id'] = 1;
        }

        $user = User::create($dados);
        return response()->json($user, 201);
    }

    /**
     * Retorna a lista de usuários cadastrados no sistema
     */
    public function list(Request $request) {
        $users = User::all();
        return response()->json($users, 200);
    }

    /**
     * Retorna os dados de um usuário pelo ID
     */
    public function get(Request $request, $id) {
        $user = User::find($id);
        return response()->json($user, 200);
    }

    /**
     * Atualiza um usuário
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password'  => 'min:6',
            'role_id'  => 'required|numeric'
        ]);

        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);

        $dados = $request->all();
        
        //Caso não seja admin, não pode editar nível, acesso e outros usuários
        if (!$request->user()->tokenCan('admin')) {
            unset($dados['admin']);
            unset($dados['role_id']);
            $id = $request->user()->id;
        }  

        $user = User::findOrFail($id);
        $user->fill($dados);
        $user->save();
        return response()->json($user, 200);
    }

    /**
     * Deleta usuário pelo id
     */
    public function delete(Request $request, $id) {
        
        //Caso não seja admin, não pode deletar outros usuários
        if (!$request->user()->tokenCan('admin')) {
            $id = $request->user()->id;
        }

        $user = User::findOrFail($id);
        $user->delete();
     
        return response()->json($user, 200);
    }

    /**
     * Solicita a recuperação de senha
     */
    public function requestRecoverPassword(Request $request) {
        
        $user = User::where('email', $request->input('email'))->firstOrFail();

        $token = Crypt::encryptString($user->id.'-'.date('Ymd'));
        $url = env('FRONT_URL', 'http://localhost:3000/') . '/recover-password/' . $token . '?email=' . $user->email;

        Mail::to($user->email)->send(new RecoverPasswordMail($user, $url));
    }

    /**
     * Solicita a recuperação de senha
     */
    public function recoverPassword(Request $request, $token) {
        
        try {            
            $validator = Validator::make($request->all(), [
                'password'  => 'min:6',
            ]);
            
            if ($validator->fails()) return response()->json($validator->errors()->all(), 400);
            
            $dados = explode('-', Crypt::decryptString($token));
            $id = $dados[0];
            $data = $dados[1];
            if ($data != date('Ymd')) return response()->json('Token expirado', 400);

            $user = User::findOrFail($id);
            $user->password = $request->input('password');
            $user->save();

            return response()->json('Senha alterada com sucesso', 200);

        } catch (DecryptException $e) {
            return response()->json('Token inválido', 400);
        }
    }





}
