# 🌐 TaskBoard — Semana 13

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-orange?logo=mysql)
![API](https://img.shields.io/badge/API-REST-0B2E59)
![UPED](https://img.shields.io/badge/UPED-Integración_de_Sistemas-0B2E59)

Proyecto desarrollado para la asignatura **Integración de Sistemas** de la **Universidad Pedagógica de El Salvador (UPED)**.

Durante la Semana 13 se agregó una API REST a TaskBoard para exponer comercios y transacciones mediante respuestas JSON controladas con API Resources.

**Estudiante:** Josthyn Stanley Cruz Vásquez  
**Carrera:** Ingeniería en Sistemas y Computación  
**Universidad:** Universidad Pedagógica de El Salvador  
**Ciclo:** 02-2026  

---

## 🎯 Objetivo de la semana

Construir una API REST para TaskBoard que permita consultar, crear y eliminar datos mediante JSON, utilizando API Resources para controlar la información expuesta y una herramienta de pruebas HTTP para verificar códigos de respuesta, validaciones y filtros.

---

# 🚪 Activación de la API

Laravel fue configurado para trabajar con rutas de API mediante:

```bash
php artisan install:api
```

Esto habilitó `routes/api.php`, cuyas rutas utilizan automáticamente el prefijo `/api`.

---

# 🏪 Primer endpoint de comercios

Se creó inicialmente:

```text
GET /api/comercios
```

devolviendo directamente los registros del modelo `Comercio`. Esta primera prueba permitió observar que se exponían campos internos como `user_id`, `created_at` y `updated_at`.

---

# 🧪 Pruebas con Insomnia

Para verificar la API se utilizó **Insomnia** con peticiones HTTP y el encabezado:

```text
Accept: application/json
```

Con Insomnia se comprobaron métodos, URLs, encabezados, cuerpos JSON y códigos de respuesta.

---

# 🧩 ComercioResource

Se creó `app/Http/Resources/ComercioResource.php` para controlar los campos públicos:

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'nombre' => $this->nombre_comercio,
        'rubro' => $this->rubro,
    ];
}
```

La respuesta pasó a utilizar una estructura controlada dentro de `data`, sin exponer campos internos.

---

# 💳 TransaccionResource

Se creó `app/Http/Resources/TransaccionResource.php`:

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'cliente' => $this->cliente_nombre,
        'monto' => (float) $this->monto,
        'estado' => $this->estado,
        'fecha' => $this->created_at->toIso8601String(),
    ];
}
```

El monto se devuelve como número y la fecha en formato ISO 8601.

---

# 🔗 Relaciones con whenLoaded()

El detalle de un comercio incluye sus transacciones solamente cuando la relación fue cargada:

```php
'transacciones' => TransaccionResource::collection(
    $this->whenLoaded('transacciones')
),
```

Por eso `GET /api/comercios` no carga transacciones, mientras `GET /api/comercios/1` sí puede incluirlas.

---

# 🛣️ apiResource para transacciones

Se creó `app/Http/Controllers/Api/TransaccionApiController.php` y se registraron las rutas con:

```php
Route::apiResource('transacciones', TransaccionApiController::class)
    ->parameters([
        'transacciones' => 'transaccion',
    ]);
```

La personalización del parámetro permite utilizar correctamente el Route Model Binding.

Las rutas principales son:

```text
GET      /api/transacciones
POST     /api/transacciones
GET      /api/transacciones/{transaccion}
PUT      /api/transacciones/{transaccion}
PATCH    /api/transacciones/{transaccion}
DELETE   /api/transacciones/{transaccion}
```

---

# ➕ Crear transacciones por API

Se creó `GuardarTransaccionApiRequest.php` para validar la API sin modificar la seguridad web de Semana 12.

Validaciones principales:

```php
'comercio_id' => ['required', 'exists:comercios,id'],
'cliente_nombre' => ['required', 'string', 'min:3', 'max:255'],
'monto' => ['required', 'numeric', 'min:0.01'],
'estado' => ['nullable', 'in:Iniciada,Completada,Fallida'],
```

Una creación válida con `POST /api/transacciones` responde `201 Created`.

---

# ⚠️ Validaciones 422

Una petición inválida responde:

```text
422 Unprocessable Entity
```

con un objeto `errors` que indica los campos incorrectos.

---

# 🗑️ Eliminar transacciones

El método `destroy()` elimina una transacción y responde:

```text
204 No Content
```

Si se intenta eliminar nuevamente el mismo registro, la API responde:

```text
404 Not Found
```

---

# 📡 Importancia de Accept: application/json

Con `Accept: application/json`, una validación incorrecta responde `422` en JSON.

Sin pedir explícitamente JSON, se comprobó una respuesta:

```text
302 Found
```

lo que demuestra la importancia de indicar el formato esperado por el cliente.

---

# 🔎 Filtro por estado

Se agregó un parámetro opcional:

```php
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
```

Ejemplo:

```text
GET /api/transacciones?estado=Completada
```

devuelve únicamente transacciones completadas.

Un estado sin coincidencias devuelve:

```json
{
  "data": []
}
```

con código `200 OK`.

---

# ✅ Códigos HTTP comprobados

| Código | Significado | Ejemplo |
|---|---|---|
| `200` | Petición correcta | GET comercios y transacciones |
| `201` | Recurso creado | POST transacción válida |
| `204` | Eliminación correcta sin cuerpo | DELETE |
| `302` | Redirección | Petición inválida sin Accept JSON |
| `404` | Recurso inexistente | Comercio o transacción no encontrada |
| `422` | Error de validación | POST con datos inválidos |

---

# 🧪 Pruebas realizadas

Se verificaron:

- `GET /api/comercios`
- `GET /api/comercios/1`
- `GET /api/comercios/999`
- `GET /api/transacciones`
- `GET /api/transacciones/1`
- `POST /api/transacciones` válido
- `POST /api/transacciones` inválido
- `DELETE /api/transacciones/{id}`
- segundo DELETE al mismo ID
- petición inválida sin `Accept: application/json`
- filtro `?estado=Completada`
- filtro sin coincidencias

---

# ⚠️ Alcance de seguridad de Semana 13

La API desarrollada esta semana todavía trabaja sin autenticación por token.

Las rutas de `routes/api.php` no dependen del login web ni de las cookies de sesión implementadas en Semana 12.

La autenticación específica de API mediante tokens se trabajará en la siguiente etapa.

---

# 🧱 Estructura principal trabajada

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── TransaccionApiController.php
│   ├── Requests/
│   │   └── GuardarTransaccionApiRequest.php
│   └── Resources/
│       ├── ComercioResource.php
│       └── TransaccionResource.php

routes/
├── api.php
└── web.php
```

---

# 🛠️ Tecnologías utilizadas

- Laravel
- PHP 8.2
- MySQL / MariaDB
- Eloquent ORM
- Laravel API Resources
- REST
- JSON
- Insomnia
- Form Requests
- Git
- GitHub
- XAMPP

---

# ▶️ Ejecución del proyecto

Clonar el repositorio:

```bash
git clone https://github.com/StanleyJv/taskboard-semana13.git
```

Entrar al proyecto:

```bash
cd taskboard-semana13
```

Instalar dependencias:

```bash
composer install
```

Configurar `.env`, generar la clave y ejecutar:

```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

La API estará disponible en:

```text
http://127.0.0.1:8000/api
```

---

# 🔗 Repositorio

https://github.com/StanleyJv/taskboard-semana13

---

**Integración de Sistemas · Ciclo 02-2026 · UPED**
