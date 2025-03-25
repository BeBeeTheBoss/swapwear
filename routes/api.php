<?php

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\MainCategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SellingProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    //main categories
    Route::group(['prefix' => '/main-categories','controller' => MainCategoryController::class], function () {
        Route::get('/{id?}','index');
        Route::post('/','store');
        Route::post('/update','update');
        Route::delete('/','destroy');
    });

    //sub categories
    Route::group(['prefix' => '/sub-categories','controller' => SubCategoryController::class], function () {
        Route::get('/{id?}','index');
        Route::post('/','store');
        Route::post('/update','update');
        Route::delete('/','destroy');
    });

    //selling products
    Route::group(['prefix' => '/selling-products','controller' => SellingProductController::class], function () {
        Route::get('/{id?}','index');
        Route::post('/','store');
        Route::post('/update','update');
        Route::delete('/','destroy');
    });

    Route::group(['prefix' => '/orders','controller' => OrderController::class],function(){
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/refund','refund');
        Route::post('/accept','accept');
        Route::post('/make-payment','makePayment');
        Route::post('/accept-payment','acceptPayment');
        Route::post('/reject','reject');
        Route::post('/delivered','delivered');
        Route::post('/received','received');
    });

    Route::group(['prefix' => '/payments', 'controller' => PaymentController::class],function(){
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/update','update');
        Route::delete('/','destroy');
    });


});
