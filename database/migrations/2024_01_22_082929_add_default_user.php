<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->insert(
            array(
                'name' => 'Boschetti',
                'email' => 'admin@admin',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
                'role' => 1,
            )
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
