<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketReplyController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/export', [TicketController::class, 'export']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::match(['put','patch'], '/tickets/{ticket}', [TicketController::class, 'update']);
    Route::post('/tickets/{ticket}/replies', [TicketReplyController::class, 'store']);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
});
