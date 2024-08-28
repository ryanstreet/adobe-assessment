<?php

use App\Http\Controllers\ApplyEditsController;
use App\Http\Controllers\AutoStraightenController;
use App\Http\Controllers\AutoToneController;
use App\Http\Controllers\GetEditedImageUrlController;
use App\Http\Controllers\GetJobStatusController;
use App\Http\Controllers\UploadPhotoController;
use Illuminate\Support\Facades\Route;

// returns view for home page.  See resources/view/welcome.php
Route::get('/', function () {
    return view('welcome');
});

Route::post('/applyEdits', ApplyEditsController::class);

Route::post('/autoStraighten', AutoStraightenController::class);

Route::post('/autoTone', AutoToneController::class);

Route::get('/getJobStatus/{jobId}', GetJobStatusController::class);

Route::get('getEditedImageName/{originalImageName}', GetEditedImageUrlController::class);

Route::post('/upload', UploadPhotoController::class);
