<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\LeaderTraitController;

Route::apiResource('/', LeaderTraitController::class);

// Route::apiResource('uploads', UploadController::class)->middleware('auth:sanctum');