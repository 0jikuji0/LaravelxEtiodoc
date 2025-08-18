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
}
