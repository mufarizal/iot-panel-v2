<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('', [AuthController::class, 'showLoginForm']);
Route::post('login', [AuthController::class, 'login'])->name('login');


Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('devices', [DeviceController::class, 'index'])->name('devices');
    Route::get('/devices/latest-data', [DeviceController::class, 'latestData'])->name('devices.latest-data');

    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/table', [HistoryController::class, 'table'])->name('history.table');
    Route::get('/history/chart', [HistoryController::class, 'chart'])->name('history.chart');

    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
});
