@extends('layouts.app')

@section('contenido')
    <h1>Iniciar sesión</h1>

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

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
        </div>

        <div class="card">
            <label>
                <input type="checkbox" name="recordar">
                Recordarme
            </label>
        </div>

        <button type="submit">
            Entrar
        </button>
    </form>
@endsection