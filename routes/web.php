<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComercioController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\EventoTransaccionController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/taskboard', function () {
    return 'Bienvenido a TaskBoard, tu pasarela de pagos.';
});

Route::get('/acerca-de', function () {
    return 'TaskBoard es una pasarela de pagos que permite a comercios afiliados recibir pagos y dar seguimiento a sus transacciones.';
});

Route::get('/contacto', function () {
    return 'Josthyn Stanley Cruz Vásquez - josthynvasquez32022@gmail.com';
});

/*
|--------------------------------------------------------------------------
| Rutas de autenticación para invitados
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/registro', [AuthController::class, 'mostrarRegistro'])
        ->name('registro');

    Route::post('/registro', [AuthController::class, 'registrar'])
        ->name('registro.store');

    Route::get('/login', [AuthController::class, 'mostrarLogin'])
        ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas por autenticación
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Rutas de comercios
    |--------------------------------------------------------------------------
    */

    Route::prefix('comercios')->name('comercios.')->group(function () {
        Route::get('/', [ComercioController::class, 'index'])->name('index');
        Route::get('/{comercio}', [ComercioController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas de transacciones
    |--------------------------------------------------------------------------
    */

    Route::get('/transacciones', [TransaccionController::class, 'index']);

    Route::get('/transaccion/{transaccion}', [TransaccionController::class, 'show']);

    Route::get('/comercios/{comercio}/transacciones/nueva',
        [TransaccionController::class, 'create']
    )->name('transacciones.create');

    Route::post('/transacciones',
        [TransaccionController::class, 'store']
    )->name('transacciones.store');

    Route::get('/transacciones/{transaccion}/editar',
        [TransaccionController::class, 'edit']
    )->name('transacciones.edit');

    Route::put('/transacciones/{transaccion}',
        [TransaccionController::class, 'update']
    )->name('transacciones.update');

    Route::delete('/transacciones/{transaccion}',
        [TransaccionController::class, 'destroy']
    )->name('transacciones.destroy');

    Route::patch('/transacciones/{transaccion}/mover/{estado}',
        [TransaccionController::class, 'moverEstado']
    )->name('transacciones.mover');

    /*
    |--------------------------------------------------------------------------
    | Rutas de eventos de transacción
    |--------------------------------------------------------------------------
    */

    Route::get('/eventos-transaccion',
        [EventoTransaccionController::class, 'index']
    );

    /*
    |--------------------------------------------------------------------------
    | Rutas de apoyo / ejemplos anteriores
    |--------------------------------------------------------------------------
    */

    Route::get('/estados', function () {
        return [
            'Iniciada',
            'Procesando',
            'Aprobada',
            'Rechazada',
            'Liquidada'
        ];
    });

    Route::get('/transaccion/demo', function () {
        return [
            'id' => 1,
            'comercio' => 'Café Amanecer',
            'monto' => 25.50,
            'moneda' => 'USD',
            'estado' => 'Aprobada'
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | Semana 9 - Laboratorio de formularios
    |--------------------------------------------------------------------------
    */

    Route::get('/practica/formulario-demo', function () {
        return view('practica.formulario_demo');
    });

    Route::post('/practica/enviar', function () {
        return 'Formulario recibido correctamente.';
    });
});