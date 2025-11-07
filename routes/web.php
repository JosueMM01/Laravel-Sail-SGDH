<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DotacionController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;

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


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    Route::resource('areas', AreaController::class)->except(['show']);
    Route::resource('dotaciones', DotacionController::class)->except(['show']);
    Route::resource('productos', ProductoController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('lotes', LoteController::class)->only(['index', 'create', 'store']);

    Route::resource('solicitudes', SolicitudController::class)->only(['index', 'show']);
    Route::patch('solicitudes/{solicitud}/estatus', [SolicitudController::class, 'updateStatus'])->name('solicitudes.update-status');

    Route::get('entregas', [EntregaController::class, 'index'])->name('entregas.index');
    Route::get('solicitudes/{solicitud}/entrega', [EntregaController::class, 'createFromSolicitud'])->name('entregas.create-from-solicitud');
    Route::post('solicitudes/{solicitud}/entrega', [EntregaController::class, 'storeFromSolicitud'])->name('entregas.store-from-solicitud');
});

require __DIR__ . '/auth.php';
