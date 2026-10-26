# 🧩 TaskBoard — Semana 12

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-orange?logo=mysql)
![UPED](https://img.shields.io/badge/UPED-Integración_de_Sistemas-0B2E59)

Proyecto desarrollado para la asignatura **Integración de Sistemas** de la **Universidad Pedagógica de El Salvador (UPED)**.

Durante la Semana 12 se incorporó autenticación de usuarios, manejo de sesiones, protección de rutas y aislamiento de datos por usuario dentro de TaskBoard.

**Estudiante:** Josthyn Stanley Cruz Vásquez  
**Carrera:** Ingeniería en Sistemas y Computación  
**Universidad:** Universidad Pedagógica de El Salvador  
**Ciclo:** 02-2026  

---

## 🎯 Objetivo de la semana

Implementar un sistema de autenticación con registro, inicio y cierre de sesión, proteger las rutas de la aplicación y garantizar que cada usuario solamente pueda consultar y modificar sus propios comercios y transacciones.

---

# 📅 Jueves — Cookies, sesiones y autenticación

## 🍪 Cookies y sesiones

Se observó el funcionamiento de las cookies generadas por Laravel desde las herramientas del navegador.

Cookies verificadas:

- `laravel_session`
- `XSRF-TOKEN`

También se comprobó que al eliminar la cookie de sesión y recargar la página Laravel genera una nueva sesión.

---

## 👤 Modelo User

Laravel ya incluía la migración inicial de usuarios y el modelo `User`.

El modelo utiliza:

```php
protected $fillable = [
    'name',
    'email',
    'password',
];
```

Los datos sensibles permanecen ocultos:

```php
protected $hidden = [
    'password',
    'remember_token',
];
```

La contraseña utiliza hash automáticamente:

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

---

## 🔐 Comprobación del hash

Se realizaron pruebas desde Tinker:

```php
use Illuminate\Support\Facades\Hash;

$h = Hash::make('secreto123');

Hash::check('secreto123', $h);
// true

Hash::check('otra', $h);
// false

Hash::make('secreto123') === $h;
// false
```

Esto permite comprobar que las contraseñas no se almacenan en texto plano.

---

## 📝 Registro de usuarios

Se creó:

```text
app/Http/Requests/RegistroRequest.php
```

Validaciones principales:

```php
'name' => ['required', 'max:255'],
'email' => ['required', 'email', 'unique:users,email'],
'password' => ['required', 'min:8', 'confirmed'],
```

Después del registro el usuario inicia sesión automáticamente:

```php
Auth::login($user);
$request->session()->regenerate();
```

---

## 🔑 Login

El inicio de sesión utiliza:

```php
Auth::attempt($credenciales, $request->boolean('recordar'));
```

Después de autenticarse se regenera la sesión:

```php
$request->session()->regenerate();
```

Y se utiliza:

```php
return redirect()->intended(route('comercios.index'));
```

para regresar al usuario a la ruta protegida que intentaba visitar.

---

## 🚪 Logout

El cierre de sesión destruye la sesión actual y genera un nuevo token CSRF:

```php
Auth::logout();

$request->session()->invalidate();
$request->session()->regenerateToken();
```

---

## 🛡️ Middleware auth y guest

Las rutas de registro e inicio de sesión utilizan:

```php
Route::middleware('guest')->group(function () {
    // registro y login
});
```

Las rutas internas de TaskBoard utilizan:

```php
Route::middleware('auth')->group(function () {
    // comercios y transacciones
});
```

De esta manera un usuario sin sesión es enviado al login cuando intenta acceder a una ruta protegida.

---

## 👋 Barra superior dinámica

El layout principal utiliza Blade para cambiar su contenido dependiendo de la sesión.

```blade
@auth
    Hola, {{ Auth::user()->name }}
@endauth

@guest
    <a href="{{ route('login') }}">Entrar</a>
    <a href="{{ route('registro') }}">Registrarse</a>
@endguest
```

También se agregó el botón para cerrar sesión.

---

## ✅ Pruebas realizadas el jueves

Se verificó correctamente:

- Registro con datos válidos.
- Bloqueo de correo duplicado.
- Contraseña mínima de 8 caracteres.
- Validación de confirmación de contraseña.
- Login con credenciales incorrectas.
- Login correcto y redirección `intended`.
- Redirección de `/comercios` hacia `/login` sin sesión.
- Destrucción de la sesión después del logout.

---

# 📅 Viernes — Aislamiento por usuario

## 🔗 Relación entre usuarios y comercios

Se agregó `user_id` a la tabla `comercios`.

```php
$table->foreignId('user_id')
    ->constrained('users')
    ->onDelete('cascade');
```

Relación en `User`:

```php
public function comercios()
{
    return $this->hasMany(Comercio::class);
}
```

Relación en `Comercio`:

```php
public function usuario()
{
    return $this->belongsTo(User::class, 'user_id');
}
```

---

## 🌱 Usuarios de prueba

El `DatabaseSeeder` crea dos usuarios:

```text
Ana
ana@taskboard.test

Carlos
carlos@taskboard.test
```

Contraseña utilizada:

```text
password123
```

Cada usuario posee un comercio distinto:

```text
Ana → Café Amanecer
Carlos → Ferretería El Tornillo
```

---

## 🏪 Comercios aislados por usuario

El listado de comercios ahora parte del usuario autenticado:

```php
$comercios = Auth::user()
    ->comercios()
    ->withCount('transacciones')
    ->orderBy('nombre_comercio')
    ->get();
```

De esta forma cada usuario solamente puede visualizar sus propios comercios.

---

## 🚫 Protección contra acceso por ID

Se agregó protección para impedir que un usuario acceda a un comercio ajeno modificando manualmente la URL.

```php
abort_unless(
    $comercio->user_id === Auth::id(),
    404
);
```

Por ejemplo:

```text
Ana → /comercios/1 ✅
Ana → /comercios/2 ❌ 404
```

---

## 💳 Protección de transacciones

Se creó un método central para autorizar transacciones:

```php
private function autorizar(Transaccion $transaccion): void
{
    abort_unless(
        $transaccion->comercio->user_id === Auth::id(),
        404
    );
}
```

La autorización se aplica antes de:

- Mostrar una transacción.
- Editarla.
- Actualizarla.
- Eliminarla.
- Cambiar su estado.

También se restringió la creación de transacciones a comercios propios.

---

## ✅ Validación segura de comercio

`GuardarTransaccionRequest` verifica que el comercio exista y además pertenezca al usuario autenticado.

```php
Rule::exists('comercios', 'id')
    ->where('user_id', $this->user()->id)
```

Esto evita que alguien modifique manualmente `comercio_id` para registrar o mover información hacia un comercio de otro usuario.

---

## 🔄 Cambio de estado protegido

Se conservaron las reglas implementadas durante Semana 11:

```text
Iniciada → Completada
Iniciada → Fallida
Fallida → Iniciada
Completada → sin transición
```

Además ahora se verifica primero que la transacción pertenezca al usuario autenticado.

---

## 🧪 Pruebas de aislamiento

Se utilizaron dos usuarios y diferentes sesiones para comprobar:

- Ana solamente ve Café Amanecer.
- Carlos solamente ve Ferretería El Tornillo.
- Ana no puede abrir el comercio de Carlos.
- Carlos no puede abrir el comercio de Ana.
- Una transacción ajena devuelve 404.
- Una transacción ajena no puede editarse.
- Una transacción ajena no puede eliminarse.
- Una transacción ajena no puede cambiar de estado.
- Las operaciones bloqueadas no modifican los datos.
- Cada usuario puede crear transacciones en su propio comercio.
- Sin sesión las rutas protegidas redirigen al login.

---

## 🧰 Verificaciones con Tinker

Se realizaron comprobaciones adicionales:

```php
Auth::attempt([
    'email' => 'ana@taskboard.test',
    'password' => 'password123'
]);
// true
```

```php
Comercio::where(
    'user_id',
    User::where('email', 'carlos@taskboard.test')->value('id')
)->count();
// 1
```

```php
Hash::check('password123', User::first()->password);
// true
```

---

## 🚦 Protección contra intentos repetidos de login

La ruta POST de login fue protegida con `throttle`:

```php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.attempt');
```

Esto permite un máximo de cinco intentos dentro de un minuto.

En la prueba realizada, el sexto intento incorrecto respondió:

```text
429 Too Many Requests
```

---

# 🔐 Seguridad implementada

Durante Semana 12 se aplicaron varias medidas de seguridad:

- Contraseñas almacenadas mediante hash.
- Regeneración del ID de sesión después del login.
- Invalidación completa de sesión durante logout.
- Protección CSRF mediante `@csrf`.
- Middleware `auth` y `guest`.
- Restricción de recursos por propietario.
- Respuesta 404 ante recursos ajenos.
- Validación de `comercio_id` en el servidor.
- Límite de intentos de login mediante `throttle`.

---

# 🧱 Estructura principal trabajada

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ComercioController.php
│   │   └── TransaccionController.php
│   └── Requests/
│       ├── GuardarTransaccionRequest.php
│       └── RegistroRequest.php
├── Models/
│   ├── Comercio.php
│   ├── Transaccion.php
│   └── User.php

database/
├── migrations/
│   └── *_add_user_id_to_comercios.php
└── seeders/
    └── DatabaseSeeder.php

resources/
└── views/
    ├── auth/
    │   ├── login.blade.php
    │   └── registro.blade.php
    └── layouts/
        └── app.blade.php

routes/
└── web.php
```

---

# 🛠️ Tecnologías utilizadas

- Laravel
- PHP 8.2
- MySQL / MariaDB
- Blade
- Eloquent ORM
- Laravel Authentication
- Laravel Middleware
- Form Requests
- Tinker
- Git
- GitHub
- XAMPP

---

# ▶️ Ejecución del proyecto

Clonar el repositorio:

```bash
git clone https://github.com/StanleyJv/taskboard-semana12.git
```

Entrar al proyecto:

```bash
cd taskboard-semana12
```

Instalar dependencias:

```bash
composer install
```

Configurar el archivo `.env` y crear la base de datos.

Ejecutar migraciones y seeders:

```bash
php artisan migrate:fresh --seed
```

Iniciar el servidor:

```bash
php artisan serve
```

Abrir:

```text
http://127.0.0.1:8000
```

---

# 🔗 Repositorio

https://github.com/StanleyJv/taskboard-semana12

---

**Integración de Sistemas · Ciclo 02-2026 · UPED**
