<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UsuarioSeeder::class,
            EstudianteSeeder::class,
            EntrevistaSeeder::class,
            DerivacionSeeder::class,
        ]);
    }
}
