<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'permissions' => ['*']],
            ['name' => 'Manager', 'permissions' => ['products.*', 'sales.*', 'customers.*', 'reports.view']],
            ['name' => 'Cashier', 'permissions' => ['pos.use', 'sales.create', 'customers.view']],
        ];

        foreach ($roles as $role) {
            Role::query()->firstOrCreate(
                ['name' => $role['name']],
                ['permissions' => $role['permissions']]
            );
        }
    }
}
