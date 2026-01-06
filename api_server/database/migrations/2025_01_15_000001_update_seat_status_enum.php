<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // If seats table does not exist (fresh install), skip this migration.
        if (!Schema::hasTable('seats')) {
            return;
        }

        // First widen enum (include both old and new values)
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('available', 'occupied', 'pending', 'Blank', 'Filled', 'In Another Basket') DEFAULT 'available'");
        
        // Then update existing data
        DB::table('seats')->where('status', 'available')->update(['status' => 'Blank']);
        DB::table('seats')->where('status', 'occupied')->update(['status' => 'Filled']);
        DB::table('seats')->where('status', 'pending')->update(['status' => 'In Another Basket']);

        // Finally, restrict enum to new values only
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('Blank', 'Filled', 'In Another Basket') DEFAULT 'Blank'");
    }

    public function down(): void
    {
        // If seats table does not exist, nothing to rollback
        if (!Schema::hasTable('seats')) {
            return;
        }

        // First widen enum back to include all values
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('available', 'occupied', 'pending', 'Blank', 'Filled', 'In Another Basket') DEFAULT 'Blank'");
        
        // Then revert data back to original statuses
        DB::table('seats')->where('status', 'Blank')->update(['status' => 'available']);
        DB::table('seats')->where('status', 'Filled')->update(['status' => 'occupied']);
        DB::table('seats')->where('status', 'In Another Basket')->update(['status' => 'pending']);

        // Finally, restrict enum back to original values
        DB::statement("ALTER TABLE seats MODIFY COLUMN status ENUM('available', 'occupied', 'pending') DEFAULT 'available'");
    }
};

