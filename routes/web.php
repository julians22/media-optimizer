<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MediaController::class, 'index']);
Route::post('/process', [MediaController::class, 'process'])->name('media.process');
Route::get('/open-file', [MediaController::class, 'openFile'])->name('media.open');
