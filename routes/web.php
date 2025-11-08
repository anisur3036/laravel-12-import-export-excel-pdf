<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/users/export', [UserController::class, 'index']);
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
Route::get('/users/export-pdf', [UserController::class, 'exportPdf'])->name('users.export-pdf');
