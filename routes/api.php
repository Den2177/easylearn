<?php

use App\Http\Controllers\MainController;
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

Route::post('/main/file', [MainController::class, 'store']);
Route::get('/main/words', [MainController::class, 'throwDictionaries']);
Route::get('/words/dictionaries', [MainController::class, 'throwDictionariesAndWords']);

Route::get('/main/dictionary/{dictionary}', [MainController::class, 'throwWordsFromDictionary']);


