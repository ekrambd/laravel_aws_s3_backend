<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;

//ajax requests
Route::post('folder-status-update', [AjaxController::class, 'folderStatusUpdate']);