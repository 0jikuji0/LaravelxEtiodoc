<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\Tariff;
use App\Models\Consultation;

class PatientController extends Controller
{
    // ... méthodes existantes ...

    /**
     * Mettre à jour les informations de paiement d'une consultation
     */
    public function list()
    {
        $patients = Patient::where('user_id', auth()->id()) // seulement les patients du médecin connecté
                           ->orderBy('last_name')
                           ->get();

        return view('patients.list', compact('patients'));
    }
    public function updatePayment(Request $request, $consultationId)
    {
        $request->validate([
            'act_type' => 'required|in:gratuit,payant',
            'price' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:pending,paid,cancelled,gratuit',
            'payment_method' => 'nullable|in:cheque,espece,paylib,autre',
            'payment_notes' => 'nullable|string|max:500'
        ]);

        try {
            $consultation = Consultation::findOrFail($consultationId);
            
            // Préparer les données de mise à jour
            $updateData = [
                'act_type' => $request->act_type,
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'payment_notes' => $request->payment_notes,
            ];

            // Gestion du prix et de la date de paiement
            if ($request->act_type === 'gratuit') {
                $updateData['price'] = 0;
                $updateData['payment_status'] = 'gratuit';
                $updateData['payment_date'] = now();
                $updateData['payment_method'] = null;
            } else {
                $updateData['price'] = $request->price;
                
                if ($request->payment_status === 'paid') {
                    $updateData['payment_date'] = now();
                } else {
                    $updateData['payment_date'] = null;
                }
            }

            $consultation->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Informations de paiement mises à jour avec succès',
                'consultation' => $consultation->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer la liste des tarifs actifs
     */
    public function getTariffs()
    {
        $tariffs = Tariff::active()->ordered()->get();
        
        return response()->json([
            'success' => true,
            'tariffs' => $tariffs
        ]);
    }
}