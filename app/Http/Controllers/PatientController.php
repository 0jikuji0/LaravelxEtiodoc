<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // Affiche la liste des patients de l'utilisateur connecté
    public function list()
    {
        $patients = Patient::where('user_id', auth()->id()) // seulement les patients du médecin connecté
                           ->orderBy('last_name')
                           ->get();

        return view('patients.list', compact('patients'));
    }
}
