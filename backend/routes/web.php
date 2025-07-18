<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MemberController;

Route::get('/', function () {
    return view('welcome');
});
