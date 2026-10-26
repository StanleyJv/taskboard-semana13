<?php

namespace App\Http\Controllers;

use App\Models\EventoTransaccion;

class EventoTransaccionController extends Controller
{
    public function index()
    {
        return EventoTransaccion::with('transaccion.comercio')->get();
    }
}