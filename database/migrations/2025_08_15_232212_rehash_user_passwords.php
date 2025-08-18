<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                // Seulement si ce n'est pas déjà bcrypt
                if (!str_starts_with($user->password, '$2y$')) {
                    $user->password = Hash::make('password123'); // mot de passe par défaut
                    $user->save();
                }
            }
        });
    }

    public function down()
    {
        // Pas de retour en arrière pour les mots de passe
    }
};