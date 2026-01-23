<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'System administrator'
        ]);

        Role::create([
            'name' => 'Client',
            'slug' => 'customer',
            'description' => 'Application client user'
        ]);
    }
}
