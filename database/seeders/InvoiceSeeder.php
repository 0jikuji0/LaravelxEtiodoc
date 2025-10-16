<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le premier utilisateur ou le créer
        $user = User::first();
        
        if (!$user) {
            echo "Aucun utilisateur trouvé. Veuillez créer un utilisateur d'abord.\n";
            return;
        }

        // Récupérer les patients de cet utilisateur
        $patients = Patient::where('user_id', $user->id)->get();
        
        if ($patients->isEmpty()) {
            echo "Aucun patient trouvé. Veuillez créer des patients d'abord.\n";
            return;
        }

        // Créer 20 factures de test
    $statuses = ['paid', 'pending', 'overdue', 'cancelled'];
    // Méthodes autorisées par l'app: cash, cheque, paylib
    $paymentMethods = ['cash', 'cheque', 'paylib'];
        
        for ($i = 1; $i <= 20; $i++) {
            $patient = $patients->random();
            $status = $statuses[array_rand($statuses)];
            // Tarifs habituels 35/40/45/50 + quelques actes gratuits
            $tariffs = [0, 35, 40, 45, 50];
            $subtotal = $tariffs[array_rand($tariffs)];
            $taxAmount = 0; // pas de TVA
            $totalAmount = $subtotal;
            
            $invoiceDate = now()->subDays(rand(0, 90));
            $dueDate = $invoiceDate->copy()->addDays(30);
            
            $invoice = Invoice::create([
                'patient_id' => $patient->id,
                'user_id' => $user->id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => $status,
                'payment_method' => ($status === 'paid' && $totalAmount > 0) ? $paymentMethods[array_rand($paymentMethods)] : null,
                'payment_date' => $status === 'paid' ? $invoiceDate->copy()->addDays(rand(1, 15)) : null,
                'notes' => $i % 3 === 0 ? 'Consultation de suivi' : null,
            ]);
            
            echo "Facture #{$invoice->invoice_number} créée pour {$patient->first_name} {$patient->last_name}\n";
        }
        
        echo "\n20 factures de test créées avec succès!\n";
    }
}
