<?php

namespace Database\Seeders;

use App\Models\Estudiante;
use Illuminate\Database\Seeder;

class EstudianteSeeder extends Seeder
{
    public function run(): void
    {
        $estudiantes = [
            // Diseño y Desarrollo de Software
            ['codigo' => 'T001', 'nombre' => 'Carlos',    'apellidos' => 'Quispe Mamani',     'email' => 'c.quispe@tecsup.edu.pe',    'carrera' => 'Diseño y Desarrollo de Software', 'ciclo' => 3],
            ['codigo' => 'T002', 'nombre' => 'Lucía',     'apellidos' => 'Flores Huanca',     'email' => 'l.flores@tecsup.edu.pe',    'carrera' => 'Diseño y Desarrollo de Software', 'ciclo' => 2],
            ['codigo' => 'T003', 'nombre' => 'Miguel',    'apellidos' => 'Condori Apaza',     'email' => 'm.condori@tecsup.edu.pe',   'carrera' => 'Diseño y Desarrollo de Software', 'ciclo' => 4],
            ['codigo' => 'T004', 'nombre' => 'Andrea',    'apellidos' => 'Vargas Cáceres',    'email' => 'a.vargas@tecsup.edu.pe',    'carrera' => 'Diseño y Desarrollo de Software', 'ciclo' => 1],
            ['codigo' => 'T005', 'nombre' => 'Fernando',  'apellidos' => 'Lazo Torres',       'email' => 'f.lazo@tecsup.edu.pe',      'carrera' => 'Diseño y Desarrollo de Software', 'ciclo' => 5],

            // Administración de Empresas
            ['codigo' => 'T006', 'nombre' => 'Valeria',   'apellidos' => 'Ramos Medina',      'email' => 'v.ramos@tecsup.edu.pe',     'carrera' => 'Administración de Empresas',      'ciclo' => 2],
            ['codigo' => 'T007', 'nombre' => 'Jorge',     'apellidos' => 'Huamani Ccopa',     'email' => 'j.huamani@tecsup.edu.pe',   'carrera' => 'Administración de Empresas',      'ciclo' => 3],
            ['codigo' => 'T008', 'nombre' => 'Patricia',  'apellidos' => 'Salas Díaz',        'email' => 'p.salas@tecsup.edu.pe',     'carrera' => 'Administración de Empresas',      'ciclo' => 1],

            // Electrónica Industrial
            ['codigo' => 'T009', 'nombre' => 'Roberto',   'apellidos' => 'Puma Ccari',        'email' => 'r.puma@tecsup.edu.pe',      'carrera' => 'Electrónica Industrial',          'ciclo' => 4],
            ['codigo' => 'T010', 'nombre' => 'Diana',     'apellidos' => 'Choque Quispe',     'email' => 'd.choque@tecsup.edu.pe',    'carrera' => 'Electrónica Industrial',          'ciclo' => 2],
            ['codigo' => 'T011', 'nombre' => 'Héctor',    'apellidos' => 'Turpo Mamani',      'email' => 'h.turpo@tecsup.edu.pe',     'carrera' => 'Electrónica Industrial',          'ciclo' => 3],

            // Mecatrónica
            ['codigo' => 'T012', 'nombre' => 'Sofía',     'apellidos' => 'Benavides Llerena', 'email' => 's.benavides@tecsup.edu.pe', 'carrera' => 'Mecatrónica',                     'ciclo' => 5],
            ['codigo' => 'T013', 'nombre' => 'Luis',      'apellidos' => 'Hancco Velarde',    'email' => 'l.hancco@tecsup.edu.pe',    'carrera' => 'Mecatrónica',                     'ciclo' => 2],
            ['codigo' => 'T014', 'nombre' => 'Gabriela',  'apellidos' => 'Paredes Zúñiga',    'email' => 'g.paredes@tecsup.edu.pe',   'carrera' => 'Mecatrónica',                     'ciclo' => 1],

            // Contabilidad
            ['codigo' => 'T015', 'nombre' => 'Rodrigo',   'apellidos' => 'Calla Huanca',      'email' => 'r.calla@tecsup.edu.pe',     'carrera' => 'Contabilidad',                    'ciclo' => 3],
        ];

        foreach ($estudiantes as $data) {
            if (!Estudiante::where('codigo', $data['codigo'])->exists()) {
                Estudiante::create(array_merge($data, [
                    'institucion_id' => 1,
                    'activo'         => 1,
                ]));
            }
        }

        $this->command->info('✅ 15 estudiantes de prueba creados.');
    }
}
