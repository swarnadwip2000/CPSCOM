<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CmsController;
use App\Http\Controllers\Api\ForgetPasswordController;
use App\Http\Controllers\Api\GroupController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('get-profile-image', [ProfileController::class, 'getProfileImage']);
        Route::post('upload-profile', [ProfileController::class, 'uploadProfile']);
        Route::post('submit-forget-password', [ForgetPasswordController::class, 'submitForgetPassword']);
        Route::post('submit-otp', [ForgetPasswordController::class, 'submitOtp']);
        Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword']);
    });
    Route::prefix('cms')->group(function () {
        Route::post('get-started', [CmsController::class, 'getStarted']);
    });

    Route::prefix('group')->group(function () {
        Route::post('group-list', [GroupController::class, 'groupList']);
        Route::post('image-upload', [GroupController::class, 'imageUpload']);
        Route::post('members', [GroupController::class, 'members']);
        Route::post('create-group', [GroupController::class, 'createGroup']);
        Route::post('media', [GroupController::class, 'media']);
        Route::post('media-image-download', [GroupController::class, 'mediaImageDownload']);
    });


});
