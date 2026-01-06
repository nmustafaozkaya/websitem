<?php

namespace Database\Seeders\Movies;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2025 filmlerini ekle
        $this->call(Movies2025Seeder::class);
    }
}
