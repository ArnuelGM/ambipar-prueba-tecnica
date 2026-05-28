<?php

use App\Http\Controllers\RouteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'routes')->name('home');
Route::resource('routes', RouteController::class);
