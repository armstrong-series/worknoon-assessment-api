<?php

use App\Http\Controllers\RefundRequestController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('refund-requests', [RefundRequestController::class, 'createRefundRequest']);
});
