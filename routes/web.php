<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\ForgetPasswordController;
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
Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('admin.forget.password');
Route::post('change-password', [ForgetPasswordController::class, 'changePassword'])->name('admin.change.password');
Route::get('forget-password/show', [ForgetPasswordController::class, 'forgetPasswordShow'])->name('admin.forget.password.show');
Route::get('reset-password/{id}/{token}', [ForgetPasswordController::class, 'resetPassword'])->name('admin.reset.password');
Route::post('change-password', [ForgetPasswordController::class, 'changePassword'])->name('admin.change.password');

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::prefix('members')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/update', [UserController::class, 'update'])->name('user.update');
        Route::get('/delete/{id}', [UserController::class, 'delete'])->name('user.delete');
        Route::post('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::get('/admin-permission/{id}', [UserController::class, 'adminPermission'])->name('user.admin-permission');
    });

    Route::prefix('admins')->group(function () {
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

    Route::prefix('password')->group(function () {
        Route::get('/', [ProfileController::class, 'password'])->name('admin.password');
        Route::post('/update', [ProfileController::class, 'passwordUpdate'])->name('admin.password.update');
    });    

    Route::prefix('super-admin')->group(function () {
        Route::get('/',[AdminController::class,'index'])->name('admin.index');
        Route::post('/store',[AdminController::class,'store'])->name('admin.store');
        Route::post('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
        Route::get('/delete/{id}', [AdminController::class, 'delete'])->name('admin.delete');
        Route::post('/update',[AdminController::class, 'update'])->name('admin.update');

        
    });    

    Route::prefix('group')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('group.index');
        Route::get('/create', [GroupController::class, 'create'])->name('group.create');
        Route::post('/store', [GroupController::class, 'store'])->name('group.store');
        Route::get('/chat/{id}', [GroupController::class, 'viewChat'])->name('group.chat');
        Route::get('/delete/{id}', [GroupController::class, 'delete'])->name('group.delete');
        Route::get('/chat/delete/{chatId}/{groupId}', [GroupController::class, 'chatDelete'])->name('group.chat.delete');
        Route::get('/group-image-update/{id}', [GroupController::class, 'groupImageUpdate'])->name('group.image.update');
        Route::post('/group-image-upload', [GroupController::class, 'groupImageUpload'])->name('group.image.upload');
    });

    Route::prefix('cms')->group(function () {
        Route::get('/user/get-started', [CmsController::class, 'userGetStarted'])->name('cms.user.get-started');
        Route::post('/get-started/update', [CmsController::class, 'userGetStartedUpdate'])->name('cms.get-started.update');
        Route::get('/sub-admin/get-started', [CmsController::class, 'adminGetStarted'])->name('cms.sub-admin.get-started');
    });
});


// Terms and Conditions
Route::get('/terms-and-conditions', function () {
    return view('frontend.terms-and-conditions');
});

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('frontend.privacy-policy');
});