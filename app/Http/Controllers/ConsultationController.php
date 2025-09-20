<?php
// app/Http/Controllers/ConsultationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Invoice;

class ConsultationController extends Controller
{
    public function updatePayment(Request $request, $id)
    {
        $data = $request->validate([
            'is_free'        => 'boolean',
            'price'          => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,check,paylib',
            'payment_status' => 'required|in:paid,pending,cancelled',
        ]);

        $consultation = Consultation::findOrFail($id);

        // Acte gratuit -> pas d'impact comptable
        if ($request->boolean('is_free')) {
            $consultation->price = 0;
            $consultation->payment_status = 'paid';
            $consultation->save();

            return response()->json(['ok' => true]);
        }

        // Paiement normal
        $consultation->price = $data['price'] ?? $consultation->price;
        $consultation->payment_status = $data['payment_status']; // "paid"
        $consultation->save();

        // (Optionnel) créer une facture si tu veux tracer la compta
        if (!empty($data['payment_method']) && $consultation->payment_status === 'paid') {
            Invoice::create([
                'patient_id'       => $consultation->patient_id,
                'user_id'          => $consultation->user_id,
                'consultation_id'  => $consultation->id,
                'invoice_number'   => 'INV-'.time(),
                'invoice_date'     => now(),
                'subtotal'         => $consultation->price,
                'tax_amount'       => 0,
                'total_amount'     => $consultation->price,
                'status'           => 'paid',
                'payment_method'   => $data['payment_method'], // cash/check/paylib
                'payment_date'     => now(),
                'notes'            => null,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function markPaid(Request $request, $id)
    {
        $data = $request->validate([
            'consultation_id' => 'required|integer',
            'amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:especes,cheque,paylib,gratuit',
            'payment_status' => 'required|in:paid,pending,cancelled,gratuit',
            'payment_date' => 'nullable|date',
        ]);

        $consultation = Consultation::findOrFail($id);

        // Vérifier que la consultation appartient à l'utilisateur connecté
        if ($consultation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Déterminer le type d'acte
        $isFree = $data['payment_method'] === 'gratuit' || $data['amount'] == 0;
        
        // Mettre à jour la consultation
        $consultation->price = $isFree ? 0 : ($data['amount'] ?? 0);
        $consultation->payment_status = $isFree ? 'gratuit' : $data['payment_status'];
        $consultation->act_type = $isFree ? 'gratuit' : 'payant';
        $consultation->payment_method = $data['payment_method'] ?? null;
        $consultation->payment_date = $data['payment_date'] ?? now();
        $consultation->payment_notes = $isFree ? 'Acte gratuit - pas d\'impact comptable' : 'Paiement enregistré via modal';
        $consultation->save();

        // TODO: Créer une facture seulement si ce n'est pas gratuit
        // if (!$isFree && $data['amount'] > 0) {
        //     Invoice::create([
        //         'patient_id'       => $consultation->patient_id,
        //         'user_id'          => $consultation->user_id,
        //         'consultation_id'  => $consultation->id,
        //         'invoice_number'   => 'INV-' . time() . '-' . $consultation->id,
        //         'invoice_date'     => now(),
        //         'subtotal'         => $data['amount'],
        //         'tax_amount'       => 0,
        //         'total_amount'     => $data['amount'],
        //         'status'           => 'paid',
        //         'payment_method'   => $data['payment_method'],
        //         'payment_date'     => $data['payment_date'] ?? now(),
        //         'notes'            => 'Paiement enregistré via modal',
        //     ]);
        // }

        return response()->json([
            'success' => true,
            'message' => $isFree ? 'Acte gratuit enregistré' : 'Paiement enregistré avec succès',
            'consultation' => $consultation
        ]);
    }
}
