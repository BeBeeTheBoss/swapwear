<?php

use Inertia\Inertia;
use App\Models\MainCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MainCategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationRequestController;
use App\Http\Controllers\SellingProductController;
use App\Http\Controllers\BannerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::redirect('/','login');
Route::get('/login', [AuthController::class, 'loginPage'])->name('loginPage');
Route::post('/login', [AuthController::class, 'adminLogin'])->name('login');

Route::middleware(['auth.check'])->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');
    Route::get('/resources',[ResourceController::class, 'index'])->name('resources');
    Route::resource('/payments', PaymentController::class)->except('show');
    Route::resource('/orders', OrderController::class)->only(['index', 'show']);
    Route::resource('/users', UserController::class)->only(['index', 'show', 'destroy']);
    Route::resource('/verification-requests', VerificationRequestController::class)
        ->only(['index', 'show'])
        ->parameters(['verification-requests' => 'verificationRequest']);
    Route::post('/verification-requests/{verificationRequest}/approve', [VerificationRequestController::class, 'approve'])->name('verification-requests.approve');
    Route::post('/verification-requests/{verificationRequest}/reject', [VerificationRequestController::class, 'reject'])->name('verification-requests.reject');
    Route::resource('/selling-products', SellingProductController::class)
        ->only(['index', 'show', 'destroy'])
        ->parameters(['selling-products' => 'sellingProduct']);
    Route::resource('/banners', BannerController::class)->except('show');

    //main categories
    Route::group(['prefix' => '/resources/main-categories','controller' => MainCategoryController::class, 'as' => 'main-categories.'], function () {
        Route::get('/','index')->name('get');
        Route::get('/create','create')->name('create');
        Route::post('/','store')->name('store');
        Route::get('/edit','edit')->name('edit');
        Route::post('/update','update')->name('update');
        Route::delete('/','destroy')->name('delete');
    });

    //sub categories
    Route::group(['prefix' => 'sub-categories','controller' => SubCategoryController::class, 'as' => 'sub-categories.'], function () {
        Route::get('/','index')->name('get');
        Route::get('/create','create')->name('create');
        Route::post('/','store')->name('store');
        Route::get('/edit','edit')->name('edit');
        route::post('/update','update')->name('update');
        Route::delete('/','destroy')->name('delete');
    });

});
