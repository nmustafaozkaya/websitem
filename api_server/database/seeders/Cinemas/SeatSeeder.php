<?php

namespace Database\Seeders\Cinemas;

use Illuminate\Database\Seeder;
use App\Models\Seat;
use App\Models\Hall;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('💺 Koltuklar oluşturuluyor...');

        $halls = Hall::all();

        if ($halls->isEmpty()) {
            $this->command->error('❌ Önce salonlar oluşturulmalı! HallSeeder çalıştır.');
            return;
        }

        $totalSeats = 0;

        foreach ($halls as $hall) {
            // Eğer bu salon için koltuklar zaten varsa atla
            if ($hall->seats()->count() > 0) {
                continue;
            }

            $capacity = $hall->capacity;
            $seatsPerRow = 10; // Her sırada 10 koltuk
            $totalRows = ceil($capacity / $seatsPerRow);
            
            $seatBatch = [];

            for ($rowIndex = 0; $rowIndex < $totalRows; $rowIndex++) {
                $row = chr(65 + $rowIndex); // A, B, C, D...
                $seatsInThisRow = min($seatsPerRow, $capacity - ($rowIndex * $seatsPerRow));
                
                for ($number = 1; $number <= $seatsInThisRow; $number++) {
                    $seatBatch[] = [
                        'hall_id' => $hall->id,
                        'row' => $row,
                        'number' => $number,
                        // Use enum values defined on seats.status ('Blank', 'Filled', 'In Another Basket')
                        'status' => 'Blank',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $totalSeats++;
                }
            }

            // Batch insert for performance
            if (!empty($seatBatch)) {
                Seat::insert($seatBatch);
            }
        }

        $this->command->info("✅ {$totalSeats} koltuk oluşturuldu.");
    }
}