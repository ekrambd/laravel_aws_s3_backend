<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileController;

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

Route::get('/', [IndexController::class, 'indexPage'])->name('index.page');

Route::post('admin-login', [AccessController::class, 'adminLogin'])->name('admin.login');

Route::get('/admin-logout', [AccessController::class, 'adminLogout'])->name('admin.logout');

Route::group(['middleware' => 'prevent-back-history'],function(){
   //dashboard
	 Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
   
   //buckets
	 Route::resource('buckets', BucketController::class);

	//folders
	 Route::resource('folders', FolderController::class);

	//files
	 Route::resource('files', FileController::class);

	 Route::get('/add-file/{id}', [FileController::class, 'addFile']);
});