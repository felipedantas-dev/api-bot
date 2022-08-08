<?php

use App\Http\Controllers\api\TelegramController;
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

Route::prefix('telegram')
->controller(TelegramController::class)
->group(function() {
    Route::post('bot/webhook', 'process');
});


Route::prefix('correios')
->controller(TelegramController::class)
->middleware('auth:api')
->group(function() {
    Route::post('bot/webhook', 'process');
});