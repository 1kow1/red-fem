<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'cargo' => 'required|in:ADMIN,SECRETARIA,MEDICO,ACADEMICO',
            'telefone' => 'nullable|string',
            'crm' => 'nullable|string',
            'especialidade' => 'nullable|in:GINECOLOGIA,ONCOLOGIA,ODONTOLOGIA,NENHUMA'
        ]);

        $user = User::create([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'cargo' => $data['cargo'],
            'telefone' => $data['telefone'] ?? null,
            'crm' => $data['crm'] ?? null,
            'especialidade' => $data['especialidade'] ?? 'NENHUMA',
            'ativo' => true,
            'temporary_password' => true
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'usuario' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        if (!$user->ativo) {
            return response()->json([
                'message' => 'Usuário inativo'
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'usuario' => $user,
            'token' => $token
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
