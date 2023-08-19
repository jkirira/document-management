<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::updateOrCreate(
                        ['slug' => Role::ADMIN_ROLE_SLUG],
                        ['name' => 'admin']
                    );

        $adminUser = User::updateOrCreate(
                        ['email' => 'admin@admin.com'],
                        [
                            'name' => 'admin',
                            'password' => Hash::make('password')
                        ]
                    );

        $adminUser->roles()->attach($adminRole->id);

    }
}
