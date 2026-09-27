<?php

use App\Http\Controllers\RefundRequestController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('refund-requests', [RefundRequestController::class, 'createRefundRequest']);
    Route::get('refund-requests', [RefundRequestController::class, 'fetchRefundRequests']);
    Route::get('refund-requests/{refundRequest}', [RefundRequestController::class, 'fetchRefundRequest']);

    Route::patch('refund-requests/{refundRequest}/decision', [RefundRequestController::class, 'decideRefundRequest']);
});
