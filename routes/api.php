<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ApiwebsiteController;


Route::get('/homeProduct', [ApiwebsiteController::class, 'homeProduct']);

Route::get('/test', function () {
    return ['status' => 'routes are working'];
});

