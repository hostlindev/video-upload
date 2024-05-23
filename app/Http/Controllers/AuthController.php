<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Obtener las credenciales del request
        $credentials = $request->only('username', 'password');

        // Buscar el usuario por username
        $user = User::where('username', $credentials['username'])->first();

        // Verificar si el usuario existe y si la contraseña es correcta
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'No Autorizado'], 401);
        }

        // Autenticar al usuario con el guard de Sanctum
        Auth::login($user);

        // Crear un token de Sanctum y devolver una respuesta vacía con las cookies de sesión
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Se ha cerrado la sesión'], 200);
    }
}
