<?php

// Fichier routes/web.php corrigé
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\MedicalHistory;
use App\Models\MedicalReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

// Redirection vers login si pas connecté
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    
    // Page de profil utilisateur
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Dashboard - affiche la liste des patients de l'utilisateur connecté
    Route::get('/dashboard', function () {
        $patients = Patient::where('user_id', auth()->id())
                          ->latest()
                          ->get();
        return view('dashboard', compact('patients'));
    })->name('dashboard');

    // Créer un nouveau patient (formulaire POST)
    Route::post('/patients', function (Request $request) {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'required|date',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        // Ajouter l'ID de l'utilisateur connecté
        $validated['user_id'] = auth()->id();
        
        $patient = Patient::create($validated);
        
        // Si c'est une requête AJAX (depuis le modal)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'patient_id' => $patient->id,
                'message' => 'Patient créé avec succès'
            ], 201);
        }
        
        return redirect()->route('dashboard')->with('success', 'Patient créé avec succès.');
    })->name('patients.store');

    // Voir les détails d'un patient AVEC TOUTES LES DONNÉES
    Route::get('/patients/{patient}', function (Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé à ce patient.');
        }

        // Charger toutes les données du patient avec ses relations
        $patient->load([
            'consultations' => function($query) {
                $query->orderBy('consultation_date', 'desc');
            },
            'medicalHistories' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('created_at', 'desc');
            },
            'medicalReports' => function($query) {
                $query->orderBy('report_date', 'desc');
            }
        ]);

        // Récupérer la consultation la plus récente ou créer un objet vide
        $currentConsultation = $patient->consultations->first();
        
        // Récupérer toutes les consultations pour JavaScript
        $consultations = $patient->consultations;
        
        // Statistiques du patient
        $stats = [
            'total_consultations' => $patient->consultations->count(),
            'last_consultation_date' => $currentConsultation ? $currentConsultation->consultation_date : null,
            'active_treatments' => 0,
            'pending_appointments' => 0
        ];

        return view('patients.show', compact('patient', 'currentConsultation', 'consultations', 'stats'));
    })->name('patients.show');

    // Mettre à jour un patient (PUT/PATCH)
    Route::put('/patients/{patient}', function (Request $request, Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
            abort(403, 'Accès non autorisé à ce patient.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'required|date',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $patient->update($validated);

        // Si c'est une requête AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Informations mises à jour avec succès'
            ]);
        }

        return redirect()->back()->with('success', 'Informations mises à jour avec succès');
    })->name('patients.update');

    // ✅ NOUVELLE ROUTE - Créer une consultation
    Route::post('/patients/{patient}/consultations', function (Request $request, Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'consultation_date' => 'required|date',
            'motif' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'clinical_examination' => 'nullable|string',
            'etiopathic_diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:normale,urgente,controle,annulee',
            'price' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending,paid,cancelled'
        ]);

        // Ajouter les IDs requis
        $validated['patient_id'] = $patient->id;
        $validated['user_id'] = auth()->id();

        $consultation = Consultation::create($validated);

        return response()->json([
            'success' => true,
            'consultation_id' => $consultation->id,
            'message' => 'Consultation enregistrée avec succès',
            'consultation' => $consultation
        ], 201);
    })->name('patients.consultations.store');

    // ✅ NOUVELLE ROUTE - Mettre à jour une consultation
    Route::put('/consultations/{consultation}', function (Request $request, Consultation $consultation) {
        // Vérifier que la consultation appartient à l'utilisateur connecté
        if ($consultation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'consultation_date' => 'sometimes|date',
            'motif' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'clinical_examination' => 'nullable|string',
            'etiopathic_diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:normale,urgente,controle,annulee',
            'price' => 'nullable|numeric|min:0',
            'payment_status' => 'sometimes|in:pending,paid,cancelled'
        ]);

        $consultation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Consultation mise à jour avec succès',
            'consultation' => $consultation
        ]);
    })->name('consultations.update');

    // ✅ NOUVELLE ROUTE - Obtenir toutes les consultations d'un patient
    Route::get('/patients/{patient}/consultations', function (Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $consultations = $patient->consultations()
            ->orderBy('consultation_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'consultations' => $consultations
        ]);
    })->name('patients.consultations.index');

    // ✅ ROUTE MISE À JOUR - Sauvegarder les antécédents
    Route::post('/patients/{patient}/medical-history', function (Request $request, Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $validated = $request->validate([
            'antecedents' => 'required|array',
            'antecedents.*.title' => 'required|string|max:255',
            'antecedents.*.description' => 'nullable|string',
        ]);

        // Supprimer les anciens antécédents actifs
        $patient->medicalHistories()->update(['is_active' => false]);

        // Ajouter les nouveaux
        foreach ($validated['antecedents'] as $antecedent) {
            if (!empty($antecedent['title'])) {
                $patient->medicalHistories()->create([
                    'category' => 'medical',
                    'title' => $antecedent['title'],
                    'description' => $antecedent['description'] ?? '',
                    'is_active' => true
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Antécédents sauvegardés avec succès'
        ]);
    })->name('patients.medical-history.store');

    // ✅ NOUVELLE ROUTE - Obtenir les antécédents d'un patient
    Route::get('/patients/{patient}/medical-history', function (Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $medicalHistory = $patient->medicalHistories()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'medical_history' => $medicalHistory
        ]);
    })->name('patients.medical-history.index');

    // ✅ NOUVELLE ROUTE - Supprimer une consultation
    Route::delete('/consultations/{consultation}', function (Consultation $consultation) {
        // Vérifier que la consultation appartient à l'utilisateur connecté
        if ($consultation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $consultation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Consultation supprimée avec succès'
        ]);
    })->name('consultations.destroy');

    // Supprimer un patient (DELETE)
    Route::delete('/patients/{patient}', function (Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }
            abort(403, 'Accès non autorisé à ce patient.');
        }

        $patient->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Patient supprimé avec succès'
            ]);
        }
        
        return redirect()->route('dashboard')->with('success', 'Patient supprimé avec succès.');
    })->name('patients.destroy');

    Route::get('/liste-patients', [PatientController::class, 'list'])->name('patients.list');
        Route::get('/squelette', function () {
        return view('patients.squelette');
    })->name('squelette.show');

    // Route pour afficher le squelette d'un patient spécifique (optionnel)
    Route::get('/patients/{patient}/squelette', function (Patient $patient) {
        // Vérifier que le patient appartient à l'utilisateur connecté
        if ($patient->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé à ce patient.');
        }

        return view('patients.squelette', compact('patient'));
    })->name('patients.squelette.show');

});

// Inclusion des routes d'authentification
require __DIR__.'/auth.php';