<?php
// Fichier : database/migrations/2025_08_20_000001_add_payment_fields_to_consultations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consultations', function (Blueprint $table) {
            // Vérifier si les colonnes existent avant de les ajouter
            if (!Schema::hasColumn('consultations', 'act_type')) {
                $table->enum('act_type', ['gratuit', 'payant'])->default('payant')->after('price');
            }
            
            if (!Schema::hasColumn('consultations', 'payment_method')) {
                $table->enum('payment_method', ['cheque', 'espece', 'paylib', 'autre'])->nullable()->after('payment_status');
            }
            
            if (!Schema::hasColumn('consultations', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('consultations', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('payment_date');
            }
        });

        // Modifier payment_status pour inclure 'gratuit'
        DB::statement("ALTER TABLE consultations MODIFY COLUMN payment_status ENUM('pending', 'paid', 'cancelled', 'gratuit') DEFAULT 'pending'");
    }

    public function down()
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['act_type', 'payment_method', 'payment_date', 'payment_notes']);
        });
        
        // Remettre l'ancien enum
        DB::statement("ALTER TABLE consultations MODIFY COLUMN payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'");
    }
};