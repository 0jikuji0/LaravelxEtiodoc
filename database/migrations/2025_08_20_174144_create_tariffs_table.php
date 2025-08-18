<?php
// Fichier : database/migrations/2025_08_20_000002_create_tariffs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insérer les tarifs par défaut
        DB::table('tariffs')->insert([
            [
                'name' => 'Consultation 35€', 
                'amount' => 35.00, 
                'is_active' => true, 
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Consultation 40€', 
                'amount' => 40.00, 
                'is_active' => true, 
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Consultation 45€', 
                'amount' => 45.00, 
                'is_active' => true, 
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Consultation 50€', 
                'amount' => 50.00, 
                'is_active' => true, 
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
};



