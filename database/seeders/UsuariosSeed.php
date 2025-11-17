<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuariosSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Usuario::create([
            'nome'      => 'Admin',
            'email'     => 'admin@teste.com',
            'password'  => '123456',
            'admin'     => true,
            'nivel_id'  => 1
        ]);

        Usuario::create([
            'nome'      => 'Especialista',
            'email'     => 'especialista@teste.com',
            'password'  => '123456',
            'admin'     => false,
            'nivel_id'  => 1
        ]);

        Usuario::create([
            'nome'      => 'Profissional',
            'email'     => 'profissional@teste.com',
            'password'  => '123456',
            'admin'     => false,
            'nivel_id'  => 2
        ]);
    }
}
