<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\StellarisController;

// Route::apiResource('/', StellarisController::class);

Route::get('/', [StellarisController::class, "updateGameData"]);

// Route::apiResource('uploads', UploadController::class)->middleware('auth:sanctum');