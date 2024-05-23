<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/* Rutas de autenticacion */
/* Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['csrf_token' => csrf_token()]);
}); */

Route::post('/login', [AuthController::class, 'login']);
Route::post('/video-upload', [VideoController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    /* Rutas para el manejo de videos */
    // Ruta para almacenar un video
    //Route::middleware(['auth:sanctum', 'checkUserRole'])->post('/video-upload', [VideoController::class, 'store']);

    /* Ruta para cerrar sesión */
    Route::post('/logout', [AuthController::class, 'logout']);
});
