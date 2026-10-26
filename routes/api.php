<?php

use Illuminate\Support\Facades\Route;
use App\Models\Comercio;
use App\Http\Resources\ComercioResource;
use App\Http\Controllers\Api\TransaccionApiController;

Route::get('/comercios', function () {
    return ComercioResource::collection(Comercio::all());
});

Route::get('/comercios/{comercio}', function (Comercio $comercio) {
    return new ComercioResource(
        $comercio->load('transacciones')
    );
});

Route::apiResource('transacciones', TransaccionApiController::class)
    ->parameters([
        'transacciones' => 'transaccion',
    ]);