<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\BlackjackController;



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
Route::get('/', function () {
    return redirect('dashboard');
});

Route::controller(LoginRegisterController::class)->group(function() {
    Route::get('/register', 'register')->name('register');
    Route::post('/store', 'store')->name('store');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/dashboard', 'dashboard')->name('dashboard');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::get('/blackjack', [BlackjackController::class, 'index'])->name('blackjack.index');
    Route::post('/blackjack/start', [BlackjackController::class, 'start'])->name('blackjack.start');
    Route::post('/blackjack/hit', [BlackjackController::class, 'hit'])->name('blackjack.hit');
    Route::post('/blackjack/dealer-turn', [BlackjackController::class, 'dealerTurn'])->name('blackjack.dealerTurn');
    Route::get('/blackjack/result', [BlackjackController::class, 'result'])->name('blackjack.result');
});
