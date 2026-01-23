<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $clientRole = Role::where('slug', 'customer')->first();

        $adminUser = User::where('email', 'admin@test.com')->first();
        $clientUser = User::where('email', 'client@test.com')->first();

        DB::table('user_role')->insert([
            'user_id' => $adminUser->id,
            'role_id' => $adminRole->id,
        ]);

        DB::table('user_role')->insert([
            'user_id' => $clientUser->id,
            'role_id' => $clientRole->id,
        ]);
    }
}
