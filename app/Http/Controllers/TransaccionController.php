<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarTransaccionRequest;
use App\Models\Comercio;
use App\Models\Transaccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransaccionController extends Controller
{
    private function autorizar(Transaccion $transaccion): void
    {
        abort_unless(
            $transaccion->comercio->user_id === Auth::id(),
            404
        );
    }

    public function index()
    {
        return Transaccion::whereHas('comercio', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with('comercio')
            ->get();
    }

    public function show(Transaccion $transaccion)
    {
        $this->autorizar($transaccion);

        return $transaccion->load('comercio');
    }

    public function create(Comercio $comercio): View
    {
        abort_unless($comercio->user_id === Auth::id(), 404);

        return view('transacciones.create', compact('comercio'));
    }

    public function store(GuardarTransaccionRequest $request)
    {
        $transaccion = Transaccion::create([
            'comercio_id' => $request->comercio_id,
            'cliente_nombre' => $request->cliente_nombre,
            'monto' => $request->monto,
            'metodo_pago' => 'No especificado',
        ]);

        return redirect()
            ->route('comercios.show', $transaccion->comercio_id)
            ->with('mensaje', 'Transacción registrada con éxito.');
    }

    public function edit(Transaccion $transaccion)
    {
        $this->autorizar($transaccion);

        $comercios = Auth::user()
            ->comercios()
            ->orderBy('nombre_comercio')
            ->get();

        return view(
            'transacciones.edit',
            compact('transaccion', 'comercios')
        );
    }

    public function update(
        GuardarTransaccionRequest $request,
        Transaccion $transaccion
    ) {
        $this->autorizar($transaccion);

        $transaccion->update($request->validated());

        return redirect()
            ->route('comercios.show', $transaccion->comercio_id)
            ->with('mensaje', 'Transacción actualizada correctamente.');
    }

    public function destroy(Transaccion $transaccion)
    {
        $this->autorizar($transaccion);

        $comercioId = $transaccion->comercio_id;
        $cliente = $transaccion->cliente_nombre;

        $transaccion->delete();

        return redirect()
            ->route('comercios.show', $comercioId)
            ->with('mensaje', "Transacción de {$cliente} eliminada.");
    }

    public function moverEstado(
        Transaccion $transaccion,
        string $estado
    ) {
        $this->autorizar($transaccion);

        $transiciones = [
            'Iniciada' => ['Completada', 'Fallida'],
            'Completada' => [],
            'Fallida' => ['Iniciada'],
        ];

        if (!in_array($estado, ['Iniciada', 'Completada', 'Fallida'])) {
            abort(400, 'Estado no válido.');
        }

        if (!in_array(
            $estado,
            $transiciones[$transaccion->estado] ?? []
        )) {
            abort(
                400,
                "No se puede mover de {$transaccion->estado} a {$estado}."
            );
        }

        $transaccion->update([
            'estado' => $estado
        ]);

        return redirect()
            ->route('comercios.show', $transaccion->comercio_id)
            ->with('mensaje', "Transacción movida a {$estado}.");
    }
}