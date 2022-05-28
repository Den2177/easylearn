<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index'])->where('{page}', '.*');
Route::get('/{page}', [MainController::class, 'index'])->where('{page}', '.*');
