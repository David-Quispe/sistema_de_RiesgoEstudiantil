<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'nombre'    => 'María',
                'apellidos' => 'Condori Quispe',
                'email'     => 'consejero1@tecsup.edu.pe',
                'password'  => Hash::make('Tecsup123!'),
                'rol'       => 'consejero',
            ],
            [
                'nombre'    => 'Juan',
                'apellidos' => 'Mamani Torres',
                'email'     => 'consejero2@tecsup.edu.pe',
                'password'  => Hash::make('Tecsup123!'),
                'rol'       => 'consejero',
            ],
            [
                'nombre'    => 'Rosa',
                'apellidos' => 'Huanca Flores',
                'email'     => 'coordinador@tecsup.edu.pe',
                'password'  => Hash::make('Tecsup123!'),
                'rol'       => 'coordinador',
            ],
            [
                'nombre'    => 'César',
                'apellidos' => 'Lazo Medina',
                'email'     => 'bienestar@tecsup.edu.pe',
                'password'  => Hash::make('Tecsup123!'),
                'rol'       => 'bienestar',
            ],
        ];

        foreach ($usuarios as $data) {
            if (!Usuario::where('email', $data['email'])->exists()) {
                Usuario::create(array_merge($data, [
                    'institucion_id' => 1,
                    'activo'         => 1,
                ]));
            }
        }

        $this->command->info('✅ 4 usuarios de prueba creados (consejero x2, coordinador, bienestar).');
    }
}
