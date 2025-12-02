<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DotacionController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReportController;

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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
});


Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:super_admin,admin_farmacia')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
        Route::patch('/users/{user}/super-admin', [UserController::class, 'updateSuperAdmin'])->name('users.update-super-admin');
        Route::post('/users/{user}/resend-invitation', [UserController::class, 'resendInvitation'])->name('users.resend-invitation');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::resource('areas', AreaController::class)->except(['show']);
        Route::get('dotaciones/areas/{area}/surtir', [DotacionController::class, 'fulfillForm'])->name('dotaciones.fulfill-form');
        Route::post('dotaciones/areas/{area}/surtir', [DotacionController::class, 'fulfill'])->name('dotaciones.fulfill');
        Route::resource('dotaciones', DotacionController::class)
            ->except(['show'])
            ->parameters(['dotaciones' => 'dotacion']);
        Route::patch('productos/{producto}/desactivar', [ProductoController::class, 'deactivate'])->name('productos.deactivate');
        Route::patch('productos/{producto}/activar', [ProductoController::class, 'activate'])->name('productos.activate');
        Route::resource('productos', ProductoController::class);
        Route::patch('proveedores/{proveedor}/desactivar', [ProveedorController::class, 'deactivate'])->name('proveedores.deactivate');
        Route::patch('proveedores/{proveedor}/activar', [ProveedorController::class, 'activate'])->name('proveedores.activate');
        Route::resource('proveedores', ProveedorController::class)->parameters([
            'proveedores' => 'proveedor',
        ]);
        Route::resource('lotes', LoteController::class)->only(['index', 'create', 'store']);

        Route::get('entregas', [EntregaController::class, 'index'])->name('entregas.index');
        Route::get('entregas/{entrega}', [EntregaController::class, 'show'])->name('entregas.show');
        Route::get('solicitudes/{solicitud}/entrega', [EntregaController::class, 'createFromSolicitud'])->name('entregas.create-from-solicitud');
        Route::post('solicitudes/{solicitud}/entrega', [EntregaController::class, 'storeFromSolicitud'])->name('entregas.store-from-solicitud');

        Route::get('reportes', [ReportController::class, 'index'])->name('reportes.index');
        Route::get('reportes/{type}/pdf', [ReportController::class, 'downloadPdf'])
            ->name('reportes.pdf')
            ->whereIn('type', ['consumo-areas', 'consumo-productos', 'solicitudes', 'entregas']);
        Route::get('reportes/{type}/excel', [ReportController::class, 'downloadExcel'])
            ->name('reportes.excel')
            ->whereIn('type', ['consumo-areas', 'consumo-productos', 'solicitudes', 'entregas']);
    });

    Route::middleware('role:super_admin,admin_farmacia,jefe_area,personal_area')->group(function () {
        Route::resource('solicitudes', SolicitudController::class)
            ->only(['index', 'show'])
            ->parameters(['solicitudes' => 'solicitud']);
        Route::patch('solicitudes/{solicitud}/estatus', [SolicitudController::class, 'updateStatus'])->name('solicitudes.update-status');
    });
});

require __DIR__ . '/auth.php';
