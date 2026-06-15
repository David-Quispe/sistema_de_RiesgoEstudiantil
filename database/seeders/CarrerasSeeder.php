<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarrerasSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            // Ingeniería y Tecnología
            ['nombre' => 'Gestión y Mantenimiento de Maquinaria Pesada',                      'grupo' => 'Ingeniería y Tecnología'],
            ['nombre' => 'Mecatrónica Industrial',                                             'grupo' => 'Ingeniería y Tecnología'],
            ['nombre' => 'Tecnología Mecánica Eléctrica',                                     'grupo' => 'Ingeniería y Tecnología'],
            ['nombre' => 'Electricidad Industrial con mención en Sistemas Eléctricos de Potencia', 'grupo' => 'Ingeniería y Tecnología'],
            ['nombre' => 'Electrónica y Automatización Industrial',                            'grupo' => 'Ingeniería y Tecnología'],
            ['nombre' => 'Procesos Químicos y Metalúrgicos',                                   'grupo' => 'Ingeniería y Tecnología'],
            ['nombre' => 'Tecnología de la Producción',                                        'grupo' => 'Ingeniería y Tecnología'],
            ['nombre' => 'Operaciones Mineras',                                                'grupo' => 'Ingeniería y Tecnología'],

            // Computación, Informática y Creatividad
            ['nombre' => 'Diseño y Desarrollo de Software',                                    'grupo' => 'Computación, Informática y Creatividad'],
            ['nombre' => 'Administración de Redes y Comunicaciones',                           'grupo' => 'Computación, Informática y Creatividad'],
            ['nombre' => 'Big Data y Ciencia de Datos',                                        'grupo' => 'Computación, Informática y Creatividad'],
            ['nombre' => 'Modelado y Animación Digital',                                       'grupo' => 'Computación, Informática y Creatividad'],
            ['nombre' => 'Diseño y Desarrollo de Simuladores y Videojuegos',                   'grupo' => 'Computación, Informática y Creatividad'],

            // Gestión y Producción
            ['nombre' => 'Producción y Gestión Industrial',                                    'grupo' => 'Gestión y Producción'],
        ];

        // Borrar las que se insertaron con encoding roto
        DB::statement("DELETE FROM CARRERAS");

        foreach ($carreras as $c) {
            DB::statement(
                "INSERT INTO CARRERAS (ID, INSTITUCION_ID, NOMBRE, GRUPO, ACTIVO)
                 VALUES (SEQ_CARRERAS.NEXTVAL, 1, ?, ?, 1)",
                [$c['nombre'], $c['grupo']]
            );
        }

        $this->command->info('✅ ' . count($carreras) . ' carreras de TECSUP insertadas correctamente.');
    }
}
