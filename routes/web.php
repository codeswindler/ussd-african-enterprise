<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loginController;
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


    Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
    Route::get('/login', [loginController::class, 'index']);
    Route::post('login', [loginController::class, 'loginSubmit'])->name('login');
    Route::get('/search', [DashboardController::class, 'search']);
    Route::post('/export', [DashboardController::class, 'exportData']);
    Route::post('/register', [loginController::class, 'registerAdmin']);
    Route::get('logout', [loginController::class, 'logOut']);


Route::get('/csrf_token', function() {
    return response()->json(['csrf_token' => csrf_token()]);
});