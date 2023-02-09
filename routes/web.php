<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\GroupController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Clear cache
Route::get('clear', function () {
    Artisan::call('optimize:clear');
    return "Optimize clear has been successfully";
});

Route::get('/', [AuthController::class, 'login'])->name('admin.login');
Route::post('/login-check', [AuthController::class, 'loginCheck'])->name('admin.login.check');  //login check

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/update', [UserController::class, 'update'])->name('user.update');
        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
        Route::post('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    });

    Route::prefix('sub-admin')->group(function () {
        Route::get('/', [SubAdminController::class, 'index'])->name('sub-admin.index');
        Route::post('/create', [SubAdminController::class, 'create'])->name('sub-admin.create');
        Route::post('/update', [SubAdminController::class, 'update'])->name('sub-admin.update');
        Route::get('/delete/{id}', [SubAdminController::class, 'delete'])->name('sub-admin.delete');
        Route::post('/edit/{id}', [SubAdminController::class, 'edit'])->name('sub-admin.edit');
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('admin.profile');
        Route::post('/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    });

    Route::prefix('group')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('group.index');
        Route::get('/chat/{id}', [GroupController::class, 'viewChat'])->name('group.chat');
        Route::get('/delete/{id}', [GroupController::class, 'delete'])->name('group.delete');
        Route::get('/chat/delete/{chatId}/{groupId}', [GroupController::class, 'chatDelete'])->name('group.chat.delete');
    });
});


