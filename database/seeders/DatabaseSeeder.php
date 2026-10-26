<?php

namespace Database\Seeders;

use App\Models\Comercio;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ana = User::create([
            'name' => 'Ana',
            'email' => 'ana@taskboard.test',
            'password' => 'password123',
        ]);

        $carlos = User::create([
            'name' => 'Carlos',
            'email' => 'carlos@taskboard.test',
            'password' => 'password123',
        ]);

        Comercio::create([
            'user_id' => $ana->id,
            'nombre_comercio' => 'Café Amanecer',
            'rubro' => 'Cafetería',
            'fecha_afiliacion' => now(),
        ]);

        Comercio::create([
            'user_id' => $carlos->id,
            'nombre_comercio' => 'Ferretería El Tornillo',
            'rubro' => 'Ferretería',
            'fecha_afiliacion' => now(),
        ]);
    }
}
