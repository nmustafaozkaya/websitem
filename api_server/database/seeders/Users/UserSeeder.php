<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Cinema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👤 Kullanıcılar oluşturuluyor...');

        $roles = Role::all()->keyBy('name');
        $firstCinema = Cinema::first();

        if ($roles->isEmpty()) {
            $this->command->error('❌ Önce roller oluşturulmalı! RoleSeeder çalıştır.');
            return;
        }

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@cinema.com',
                'password' => bcrypt('password'),
                'cinema_id' => $firstCinema?->id,
                'role_id' => $roles['admin']->id,
                'phone' => '05001234567',
                'is_active' => true
            ],
            [
                'name' => 'Test Customer',
                'email' => 'customer@cinema.com',
                'password' => bcrypt('password'),
                'cinema_id' => null,
                'role_id' => $roles['customer']->id,
                'phone' => '05009876543',
                'is_active' => true
            ]
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('✅ ' . count($users) . ' kullanıcı oluşturuldu.');
        $this->command->info('🔑 Test hesapları:');
        $this->command->info('   Admin: admin@cinema.com / password');
        $this->command->info('   Customer: customer@cinema.com / password');
    }
}