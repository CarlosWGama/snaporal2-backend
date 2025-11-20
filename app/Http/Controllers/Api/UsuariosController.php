<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RecuperarSenhaMail;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class UsuariosController extends Controller
{
    /**
     * Realiza o login do usuário
     **/
    public function login(Request $request) {
        if (Auth::attempt($request->only(['email', 'password']))) {
            $usuario = Auth::user();
            
            //Permissões
            $permissoes = [];
            if ($usuario->admin) $permissoes[] = 'admin';
            if ($usuario->nivel_id == 1) $permissoes[] = 'profissional';
            else $permissoes[] = 'especialista';
            
            $token = $usuario->createToken('token', $permissoes, now()->addWeek(4))->plainTextToken;
            return response()->json(['token' => $token, 'usuario' => $usuario], 200);
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
    public function perfil(Request $request) {
        return response()->json($request->user(), 200);
    }

    /**
     * Cria uma nova conta de usuário
     **/
    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
            'nome'      => 'required',
            'email'     => 'required|email|unique:usuarios,email',
            'password'  => 'required|min:6',
            'nivel_id'  => 'required|numeric'
        ]);
 
        //Falha na autenticação
        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);
        
        //Cadastrar
        $dados = $request->only(['nome', 'email', 'password', 'nivel_id', 'admin']);
        if (!$request->user()->tokenCan('admin')) {
            $dados['admin'] = false;
        }

        $usuario = Usuario::create($dados);
        return response()->json($usuario, 201);
    }

    /**
     * Retorna a lista de usuários cadastrados no sistema
     */
    public function list(Request $request) {
        $usuarios = Usuario::all();
        return response()->json($usuarios, 200);
    }

    /**
     * Retorna os dados de um usuário pelo ID
     */
    public function get(Request $request, $id) {
        $usuario = Usuario::find($id);
        return response()->json($usuario, 200);
    }

    /**
     * Atualiza um usuário
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'nome'      => 'required',
            'email'     => ['required', 'email', Rule::unique('usuarios')->ignore($id)],
            'password'  => 'min:6',
            'nivel_id'  => 'required|numeric'
        ]);

        if ($validator->fails()) return response()->json($validator->errors()->all(), 400);

        $dados = $request->all();
        
        //Caso não seja admin, não pode editar nível, acesso e outros usuários
        if (!$request->user()->tokenCan('admin')) {
            unset($dados['admin']);
            unset($dados['nivel_id']);
            $id = $request->user()->id;
        }  

        $usuario = Usuario::findOrFail($id);
        $usuario->fill($dados);
        $usuario->save();
        return response()->json($usuario, 200);
    }

    /**
     * Deleta usuário pelo id
     */
    public function delete(Request $request, $id) {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();
     
        return response()->json($usuario, 200);
    }

    /**
     * Solicita a recuperação de senha
     */
    public function solicitarRecuperacaoSenha(Request $request) {
        
        $usuario = Usuario::where('email', $request->input('email'))->firstOrFail();

        $token = Crypt::encryptString($usuario->id.'-'.date('Ymd'));
        $url = env('FRONT_WEB_URL', 'en_US') . '/recuperar-senha/' . $token . '?email=' . $usuario->email;

        Mail::to($usuario->email)->send(new RecuperarSenhaMail($usuario, $url));
    }

    /**
     * Solicita a recuperação de senha
     */
    public function recuperarSenha(Request $request, $token) {
        
        try {            
            $validator = Validator::make($request->all(), [
                'password'  => 'min:6',
            ]);
            
            if ($validator->fails()) return response()->json($validator->errors()->all(), 400);
            
            $dados = explode('-', Crypt::decryptString($token));
            $id = $dados[0];
            $data = $dados[1];
            if ($data != date('Ymd')) return response()->json('Token expirado', 400);

            $usuario = Usuario::findOrFail($id);
            $usuario->password = $request->input('password');
            $usuario->save();

            return response()->json('Senha alterada com sucesso', 200);

        } catch (DecryptException $e) {
            return response()->json('Token inválido', 400);
        }
    }





}
