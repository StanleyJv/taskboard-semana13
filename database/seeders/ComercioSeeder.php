<?php

namespace Database\Seeders;

use App\Models\Comercio;
use Illuminate\Database\Seeder;

class ComercioSeeder extends Seeder
{
    public function run(): void
    {
        $cafeAmanecer = Comercio::create([
            'nombre_comercio' => 'Café Amanecer',
            'rubro' => 'Restaurante',
            'telefono' => '2222-1234',
            'fecha_afiliacion' => now(),
        ]);

        $cafeAmanecer->transacciones()->createMany([
            [
                'monto' => 45.00,
                'moneda' => 'USD',
                'cliente_nombre' => 'María López',
                'metodo_pago' => 'Tarjeta',
                'estado' => 'Completada',
            ],
            [
                'monto' => 12.50,
                'moneda' => 'USD',
                'cliente_nombre' => 'Juan Pérez',
                'metodo_pago' => 'Tarjeta',
                'estado' => 'Iniciada',
            ],
        ]);

        Comercio::create([
            'nombre_comercio' => 'Ferretería El Tornillo',
            'rubro' => 'Ferretería',
            'telefono' => '2222-5678',
            'fecha_afiliacion' => now(),
        ]);

        $pupuseria = Comercio::create([
            'nombre_comercio' => 'Pupusería Doña Marta',
            'rubro' => 'Restaurante',
            'telefono' => '2222-9012',
            'fecha_afiliacion' => now(),
        ]);

        $pupuseria->transacciones()->create([
            'monto' => 8.00,
            'moneda' => 'USD',
            'cliente_nombre' => 'Carlos Ramírez',
            'metodo_pago' => 'Efectivo',
            'estado' => 'Fallida',
        ]);
    }
}