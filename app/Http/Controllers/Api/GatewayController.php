<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    public function scan(Request $request, $type) {
        if (!in_array($type, ['cancer', 'leucoplasia', 'eritroplasia', 'qa']))
            return response()->json('Caminho inválido', 400);
        
        $baseUrl = env('API_IA_URL', 'http://localhost:5000/');
        $url = "$baseUrl/$type";

        // 3. Prepara a requisição 
        $method = $request->method();
        $body = $request->all();
        
        // Captura headers importantes (ex: Bearer Token)
        //$headers = $request->header();
        // Remove o 'host' para evitar conflito no destino
        //unset($headers['host']); 
        $headers['Content-Type'] = "application/json";

        // O asJson() garante que o Content-Type seja application/json
        $response = Http::withHeaders($headers)
                        ->post($url, $body);

        // 5. Retorna a resposta do microsserviço para o cliente
        return response($response->body(), $response->status())
                ->withHeaders($response->headers());
    }
}
