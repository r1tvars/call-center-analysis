<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

Route::post('/upload', [UploadController::class, 'store'])->name('upload');

Route::get('/', function () {
    return view('home');
});
