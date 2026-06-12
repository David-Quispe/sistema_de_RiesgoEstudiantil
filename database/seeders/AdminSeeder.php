<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (Usuario::where('email', 'admin@tecsup.edu.pe')->exists()) {
            $this->command->info('✅ Admin ya existe, se omite.');
            return;
        }

        Usuario::create([
            'institucion_id' => 1,
            'nombre'         => 'Administrador',
            'apellidos'      => 'SMER',
            'email'          => 'admin@tecsup.edu.pe',
            'password'       => Hash::make('Admin1234!'),
            'rol'            => 'admin',
            'activo'         => 1,
        ]);

        $this->command->info('✅ Admin creado: admin@tecsup.edu.pe / Admin1234!');
    }
}
