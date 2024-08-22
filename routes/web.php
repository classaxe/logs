<?php

use App\Http\Controllers\ChangesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'callsigns']
)->name('callsigns');

Route::get('/changes', [ChangesController::class, 'index']
)->name('changes');

Route::get('/logs/fetch', [LogsController::class, 'logsFetch']
)->middleware(['auth', 'verified'])->name('logs.fetch');

Route::get('/logs/{callsign}', [LogsController::class, 'logsPage']
)->name('logs.page');

Route::get('/logs/{callsign}/logs', [LogsController::class, 'logs']
)->name('logs');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::post('/', [HomeController::class, 'update']
    )->name('callsigns');
});

require __DIR__.'/auth.php';
