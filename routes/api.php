<?php

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SolicitudController as ApiSolicitudController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use Illuminate\Support\Facades\Route;

Route::post('auth/token', [AuthTokenController::class, 'store'])->name('api.auth.token.store');
Route::post('auth/google', [AuthTokenController::class, 'storeFromGoogle'])->name('api.auth.google.store');

Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::delete('auth/token', [AuthTokenController::class, 'destroy'])->name('api.auth.token.destroy');

    Route::get('notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::patch('notifications/{notification}', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');

    Route::get('productos', [ApiProductController::class, 'index'])->name('api.productos.index');

    Route::get('solicitudes', [ApiSolicitudController::class, 'index'])->name('api.solicitudes.index');
    Route::post('solicitudes', [ApiSolicitudController::class, 'store'])->name('api.solicitudes.store');
    Route::get('solicitudes/{solicitud}', [ApiSolicitudController::class, 'show'])->name('api.solicitudes.show');
    Route::patch('solicitudes/{solicitud}/estatus', [ApiSolicitudController::class, 'updateStatus'])->name('api.solicitudes.update-status');
});
