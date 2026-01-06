<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👥 Roller oluşturuluyor...');

        $roles = [
            [
                'name' => 'admin',
                'description' => 'Cinema manager - full management permissions'
            ],
            [
                'name' => 'customer',
                'description' => 'Customer - basic viewing permissions'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']], 
                $role
            );
        }

        $this->command->info('✅ ' . count($roles) . ' rol oluşturuldu.');
    }
}