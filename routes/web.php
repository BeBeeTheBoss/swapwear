<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\DashboardController;

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

});
