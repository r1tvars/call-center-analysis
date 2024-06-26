<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AzureBlobController;
use App\Http\Controllers\AzureBatchTranscriptionController;
use App\Http\Controllers\CallUploadedController;

Route::post('/upload', [AzureBlobController::class, 'upload'])->name('upload');

Route::get('/', function () {
    return view('home');
});

// Route::post('/uploaded', [AzureBlobController::class, 'upload'])->name('upload');
Route::get('/uploaded', [CallUploadedController::class, 'index'])->name('index');
Route::get('/card/{id}', [CallUploadedController::class, 'getFirst'])->name('getFirsts');

Route::post('/start-transcription', [AzureBatchTranscriptionController::class, 'startTranscription'])->name('startTranscription');


Route::get('call-record/{id}', [CallUploadedController::class, 'info'])->name('info');