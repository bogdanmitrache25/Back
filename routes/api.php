<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;


Route::get('/videos',  [VideoController::class, 'index']);
Route::get('/videos/{id}',  [VideoController::class, 'show']);
