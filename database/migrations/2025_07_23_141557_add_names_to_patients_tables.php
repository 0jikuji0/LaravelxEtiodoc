<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNamesToPatientsTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('patients')) {
            Schema::create('patients', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone')->nullable();
                $table->date('birthdate')->nullable();
                $table->timestamps();
            });
        } else {
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
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
}
