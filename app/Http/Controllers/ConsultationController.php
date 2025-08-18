<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Consultation;

class PatientController extends Controller
{
    public function show($id)
    {
        // Récupérer le patient
        $patient = Patient::findOrFail($id);
        
        // Récupérer toutes les consultations du patient
        $consultations = Consultation::where('patient_id', $id)
            ->orderBy('consultation_date', 'desc')
            ->get();
        
        // Récupérer la dernière consultation ou consultation en cours
        $currentConsultation = $consultations->first();
        
        // Optionnel : Récupérer les antécédents depuis une table medical_history
        // $antecedents = MedicalHistory::where('patient_id', $id)->get();
        
        return view('patients.show', compact('patient', 'consultations', 'currentConsultation'));
    }
}