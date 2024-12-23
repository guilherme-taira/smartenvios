<?php

use App\Http\Controllers\smartEnvios\etiquetaController;
use App\Http\Controllers\smartEnvios\requestSmartEnviosController;
use App\Http\Controllers\smartEnvios\requestUploadController;
use App\Http\Controllers\smartEnvios\testeController;
use App\Http\Controllers\SmartEnvios\webhook_tray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post("etiqueta",[requestUploadController::class,'getEtiqueta']);
    Route::post('notification',[webhook_tray::class,'webhook']);
});
