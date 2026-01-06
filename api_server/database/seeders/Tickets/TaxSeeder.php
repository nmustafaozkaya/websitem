<?php

namespace Database\Seeders\Tickets;

use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxSeeder extends Seeder
{
    public function run()
    {
        Tax::create([
            'name' => 'KDV',
            'type' => 'percentage',
            'rate' => 20.00,
            'status' => 'inactive',
            'priority' => 1,
            'description' => 'Katma Değer Vergisi %20'
        ]);

        Tax::create([
            'name' => 'Hizmet Bedeli',
            'type' => 'fixed',
            'rate' => 2.00,
            'status' => 'active',
            'priority' => 2,
            'description' => 'Bilet başına hizmet bedeli'
        ]);

        Tax::create([
            'name' => 'İşlem Ücreti',
            'type' => 'fixed_total',
            'rate' => 5.00,
            'status' => 'inactive',
            'priority' => 3,
            'description' => 'Toplam işlem ücreti'
        ]);
    }
}