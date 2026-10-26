@extends('layouts.app')

@section('contenido')
    <h1>Crear cuenta</h1>

    <form method="POST" action="{{ route('registro.store') }}">
        @csrf

        <div class="card">
            <label for="name">Nombre</label><br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
            >

            @error('name')
                <div style="color: red;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="card">
            <label for="email">Correo</label><br>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
            >

            @error('email')
                <div style="color: red;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="card">
            <label for="password">Contraseña</label><br>
            <input
                type="password"
                id="password"
                name="password"
            >

            @error('password')
                <div style="color: red;">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="card">
            <label for="password_confirmation">Confirmar contraseña</label><br>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
            >
        </div>

        <button type="submit">
            Crear cuenta
        </button>
    </form>
@endsection