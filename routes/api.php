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

    Route::post('bot/fNn8hER6RRpMCjYzcFIcU4eIm58WcAQE/webhook', 'process');

});