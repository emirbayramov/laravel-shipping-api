<?php

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
Route::post('/register', [\App\Http\Controllers\ApiAuthController::class,'register']);
Route::post('/login', [\App\Http\Controllers\ApiAuthController::class,'login']);
Route::post('/logout', [\App\Http\Controllers\ApiAuthController::class,'logout']);
Route::get('/getShippingList', [\App\Http\Controllers\ShippingApiController::class,'getShippingList']);
Route::post('/addShipping', [\App\Http\Controllers\ShippingApiController::class,'addShipping']);
Route::post('/calculatePrice', [\App\Http\Controllers\ShippingApiController::class,'calculatePrice']);

