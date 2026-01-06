<?php

// app/Enums/SeatStatus.php
namespace App\Enums;

enum SeatStatus: string
{
    case AVAILABLE = 'Blank';
    case OCCUPIED = 'Filled';
    case PENDING = 'In Another Basket';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Blank',
            self::OCCUPIED => 'Filled',
            self::PENDING => 'In Another Basket',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::AVAILABLE => 'green',
            self::OCCUPIED => 'gray',
            self::PENDING => 'pink',
        };
    }
}