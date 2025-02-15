<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/recolectaInv',[StatsController::class, 'recolectaInv']); 

Route::get('/datos',[StatsController::class, 'recolectaStat']); 

Route::get('/almacena',[StatsController::class, 'almacenaStat']); 