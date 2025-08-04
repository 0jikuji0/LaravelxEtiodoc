<?php

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Redirection vers login si pas connecté
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    
    // Dashboard - affiche la liste des patients
    Route::get('/dashboard', function () {
        $patients = Patient::latest()->get();
        return view('dashboard', compact('patients'));
    })->name('dashboard');

    // Créer un nouveau patient (formulaire POST)
    Route::post('/patients', function (Request $request) {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'birthdate' => 'nullable|date',
        ]);
        
        Patient::create($request->only('first_name', 'last_name', 'phone', 'birthdate'));
        
        return redirect()->route('dashboard')->with('success', 'Patient créé avec succès.');
    })->name('patients.store');

    // Voir les détails d'un patient
    Route::get('/patients/{patient}', function (Patient $patient) {
        return view('patients.show', compact('patient'));
    })->name('patients.show');

    // Supprimer un patient (DELETE)
    Route::delete('/patients/{patient}', function (Patient $patient) {
        $patient->delete();
        return redirect()->route('dashboard')->with('success', 'Patient supprimé avec succès.');
    })->name('patients.destroy');

    // Routes pour le profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Inclusion des routes d'authentification
require __DIR__.'/auth.php';