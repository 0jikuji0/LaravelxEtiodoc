<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('patients', function (Blueprint $table) {
        if (!Schema::hasColumn('patients', 'first_name')) {
            $table->string('first_name')->nullable();
        }
        if (!Schema::hasColumn('patients', 'last_name')) {
            $table->string('last_name')->nullable();
        }
        if (!Schema::hasColumn('patients', 'phone')) {
            $table->string('phone')->nullable();
        }
        if (!Schema::hasColumn('patients', 'birthdate')) {
            $table->date('birthdate')->nullable();
        }
        // ajoute d'autres champs ici si besoin
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            //
        });
    }
};
