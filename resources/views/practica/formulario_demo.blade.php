<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sandbox de formulario</title>
</head>
<body>

    <h1>Laboratorio de formularios</h1>

    <form action="/practica/enviar" method="POST">
        @csrf   
        {{-- Primero déjalo SIN @csrf para provocar el error 419 --}}

        <label for="cliente">Cliente</label>
        <input
            id="cliente"
            name="cliente_nombre"
            type="text"
        >
        <br><br>

        <label for="monto">Monto</label>
        <input
            id="monto"
            name="monto"
            type="number"
            step="0.01"
        >
        <br><br>

        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="Iniciada">Iniciada</option>
            <option value="Completada">Completada</option>
        </select>
        <br><br>

        <label for="correo">Correo de contacto</label>
        <input
            id="correo"
            name="correo_contacto"
            type="email"
        >
        <br><br>

        <label for="recurrente">
            ¿Es una transacción recurrente?
        </label>

        <input
            id="recurrente"
            name="recurrente"
            type="checkbox"
        >
        <br><br>

        <button type="submit">
            Enviar (modo prueba)
        </button>

    </form>

</body>
</html>