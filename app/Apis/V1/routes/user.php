<?php
use App\Apis\V1\Users\Http\Controllers\RegisterController;
use App\Apis\V1\Users\Http\Controllers\LoginController;
use App\Apis\V1\Users\Http\Controllers\UsersController;

Route::prefix('/api/v1')
    ->group(function () {
        Route::post("user/register",[RegisterController::class, 'register']);
        Route::post("user/login",[LoginController::class, 'login']);

        Route::middleware("auth:sanctum")->group(function () {
            Route::resource('users',UsersController::class);
        });
    });
