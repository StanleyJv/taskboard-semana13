<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuardarTransaccionApiRequest;
use App\Http\Resources\TransaccionResource;
use App\Models\Transaccion;

class TransaccionApiController extends Controller
{
public function index()
{
    $estado = request()->query('estado');

    $transacciones = Transaccion::when(
        $estado,
        fn ($query) => $query->where('estado', $estado)
    )
        ->latest()
        ->get();

    return TransaccionResource::collection($transacciones);
}

    public function store(GuardarTransaccionApiRequest $request)
    {
        $datos = $request->validated();

        $transaccion = Transaccion::create([
            'comercio_id' => $datos['comercio_id'],
            'cliente_nombre' => $datos['cliente_nombre'],
            'monto' => $datos['monto'],
            'moneda' => 'USD',
            'metodo_pago' => 'No especificado',
            'estado' => $datos['estado'] ?? 'Iniciada',
        ]);

        return (new TransaccionResource($transaccion))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Transaccion $transaccion)
    {
        return new TransaccionResource($transaccion);
    }

    public function destroy(Transaccion $transaccion)
    {
        $transaccion->delete();

        return response()->noContent();
    }
}