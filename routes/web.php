<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AzureBlobController;

Route::post('/upload', [AzureBlobController::class, 'upload'])->name('upload');

Route::get('/', function () {
    return view('home');
});
