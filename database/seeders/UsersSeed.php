<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@teste.com',
            'password'  => '123456',
            'admin'     => true,
            'role_id'  => 1
        ]);

        User::create([
            'name'      => 'Especialista',
            'email'     => 'especialista@teste.com',
            'password'  => '123456',
            'admin'     => false,
            'role_id'  => 1
        ]);

        User::create([
            'name'      => 'Profissional',
            'email'     => 'profissional@teste.com',
            'password'  => '123456',
            'admin'     => false,
            'role_id'  => 2
        ]);
    }
}
