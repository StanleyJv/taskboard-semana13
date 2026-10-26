@extends('layouts.app')

@section('titulo', 'Nueva transacción')

@section('contenido')

    <h1>Nueva transacción</h1>

    <p class="meta">
        Comercio: {{ $comercio->nombre_comercio }}
    </p>

<form action="{{ route('transacciones.store') }}" method="POST">

        @csrf

        <input
            type="hidden"
            name="comercio_id"
            value="{{ $comercio->id }}"
        >

        <label for="cliente">Cliente</label>
        <input
            id="cliente"
            name="cliente_nombre"
            type="text"
            value="{{ old('cliente_nombre') }}"
        >

        @error('cliente_nombre')
            <span class="error" style="color:red;">
                {{ $message }}
            </span>
        @enderror

        <br><br>

        <label for="monto">Monto</label>
        <input
            id="monto"
            name="monto"
            type="number"
            step="0.01"
            value="{{ old('monto') }}"
        >

        @error('monto')
            <span class="error" style="color:red;">
                {{ $message }}
            </span>
        @enderror

        <br><br>

        <button type="submit">
            Registrar
        </button>

    </form>

@endsection