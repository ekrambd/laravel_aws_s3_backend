<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;

//ajax requests
Route::post('folder-status-update', [AjaxController::class, 'folderStatusUpdate']);
Route::post('bucket-folders', [AjaxController::class, 'bucketFolders']);
Route::post('upload-file', [AjaxController::class, 'uploadFile'])->name('upload.file');
Route::post('/upload/cancel', [UploadController::class, 'cancel'])->name('upload.cancel');
