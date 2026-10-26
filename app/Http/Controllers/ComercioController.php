<?php

namespace App\Http\Controllers;

use App\Models\Comercio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ComercioController extends Controller
{
    public function index(Request $request): View
    {
        $comercios = Auth::user()
            ->comercios()
            ->when(
                $request->buscar,
                fn ($q) => $q->where(
                    'nombre_comercio',
                    'like',
                    "%{$request->buscar}%"
                )
            )
            ->withCount('transacciones')
            ->orderBy('nombre_comercio')
            ->get();

        return view('comercios.index', compact('comercios'));
    }

public function show(Comercio $comercio): View
{
    abort_unless($comercio->user_id === Auth::id(), 404);

    $comercio->load('transacciones');

    return view('comercios.show', compact('comercio'));
}
}