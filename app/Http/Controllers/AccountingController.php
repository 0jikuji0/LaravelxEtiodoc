<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountingController extends Controller
{
    /**
     * Affiche le tableau de bord de comptabilité
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $now = now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();
        
        // Filtres
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        
        // Query de base
        $query = Invoice::with(['patient', 'user'])
            ->where('user_id', $userId);
        
        // Appliquer les filtres
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($startDate) {
            $query->where('invoice_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('invoice_date', '<=', $endDate);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Récupérer les factures avec pagination
        $invoices = $query->latest('invoice_date')->paginate(15);
        
        // Statistiques globales
        $stats = [
            // Exclure les actes gratuits (total_amount <= 0)
            'total_revenue' => Invoice::where('user_id', $userId)
                ->where('status', 'paid')
                ->where('total_amount', '>', 0)
                ->sum('total_amount'),
            
            'pending_amount' => Invoice::where('user_id', $userId)
                ->where('status', 'pending')
                ->where('total_amount', '>', 0)
                ->sum('total_amount'),
            
            'overdue_amount' => Invoice::where('user_id', $userId)
                ->where('status', 'overdue')
                ->where('total_amount', '>', 0)
                ->sum('total_amount'),
            
            'total_invoices' => Invoice::where('user_id', $userId)->count(),
            
            'paid_invoices' => Invoice::where('user_id', $userId)
                ->where('status', 'paid')
                ->count(),
            
            'pending_invoices' => Invoice::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),
            
            'overdue_invoices' => Invoice::where('user_id', $userId)
                ->where('status', 'overdue')
                ->count(),
            // CA ce mois et delta vs mois dernier
            'this_month_revenue' => Invoice::where('user_id', $userId)
                ->where('status', 'paid')
                ->where('total_amount', '>', 0)
                ->whereBetween('invoice_date', [$thisMonthStart, $now])
                ->sum('total_amount'),
            'last_month_revenue' => Invoice::where('user_id', $userId)
                ->where('status', 'paid')
                ->where('total_amount', '>', 0)
                ->whereBetween('invoice_date', [$lastMonthStart, $lastMonthEnd])
                ->sum('total_amount'),
            // Consultations basées sur la table consultations (pas sur les factures)
            'total_consultations' => Consultation::where('user_id', $userId)->count(),
            'consultations_this_month' => Consultation::where('user_id', $userId)
                ->whereBetween('consultation_date', [$thisMonthStart, $now])
                ->count(),
            'consultations_last_month' => Consultation::where('user_id', $userId)
                ->whereBetween('consultation_date', [$lastMonthStart, $lastMonthEnd])
                ->count(),
            // Actes gratuits (consultations à 0 ou non renseignées)
            'free_acts' => Consultation::where('user_id', $userId)->where(function($q){
                $q->whereNull('price')->orWhere('price', '<=', 0);
            })->count(),
        ];

        // Delta CA vs mois dernier
        $stats['revenue_delta'] = ($stats['last_month_revenue'] ?? 0) > 0
            ? round((($stats['this_month_revenue'] - $stats['last_month_revenue']) / max(1, $stats['last_month_revenue'])) * 100, 1)
            : null;
        $stats['free_acts_percent'] = ($stats['total_consultations'] ?? 0) > 0
            ? round(($stats['free_acts'] / $stats['total_consultations']) * 100, 1)
            : 0;
        
        // Statistiques mensuelles (12 derniers mois)
        $monthlyStats = Invoice::where('user_id', $userId)
            ->where('invoice_date', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN total_amount > 0 THEN total_amount ELSE 0 END) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
        
        // Revenus par statut
        $revenueByStatus = Invoice::where('user_id', $userId)
            ->select('status', DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');
        
        // Répartition par moyen de paiement (hors actes gratuits)
        $paymentMethodTotals = Invoice::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('total_amount', '>', 0)
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $paymentTotalSum = $paymentMethodTotals->sum();
        $paymentMethodPercents = $paymentTotalSum > 0
            ? $paymentMethodTotals->map(function ($v) use ($paymentTotalSum) { return round(($v / $paymentTotalSum) * 100, 1); })
            : collect();

        // Transactions récentes (10 dernières)
        $recentInvoices = Invoice::with('patient')
            ->where('user_id', $userId)
            ->orderByDesc('invoice_date')
            ->limit(10)
            ->get();

        // Résumé mensuel (6 derniers mois)
        $monthlyBreakdown = Invoice::where('user_id', $userId)
            ->where('invoice_date', '>=', $now->copy()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN total_amount > 0 THEN total_amount ELSE 0 END) as revenue'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN total_amount <= 0 THEN 1 ELSE 0 END) as free_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Liste des patients de l'utilisateur pour ajout rapide
        $patients = Patient::where('user_id', $userId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        // Tarifs pré-enregistrés
        $presetTariffs = [35, 40, 45, 50];

        return view('accounting.index', compact(
            'invoices',
            'stats',
            'monthlyStats',
            'revenueByStatus',
            'paymentMethodTotals',
            'paymentMethodPercents',
            'patients',
            'presetTariffs',
            'recentInvoices',
            'monthlyBreakdown'
        ));
    }
    
    /**
     * Affiche les détails d'une facture
     */
    public function show($id)
    {
        $invoice = Invoice::with(['patient', 'user', 'items', 'consultation'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        
        return view('accounting.show', compact('invoice'));
    }
    
    /**
     * Met à jour le statut d'une facture
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,cancelled,overdue',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string'
        ]);
        
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);
        
        $invoice->update($validated);
        
        return redirect()->back()->with('success', 'Statut de la facture mis à jour avec succès');
    }

    /**
     * Création rapide d'une facture avec tarifs pré-enregistrés.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'amount' => 'required|numeric', // 0 pour acte gratuit
            'invoice_date' => 'nullable|date',
            // Adapter au schéma DB (draft,sent,paid,overdue,cancelled)
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            // Accept any string from the UI, we'll normaliser côté serveur
            'payment_method' => 'nullable|string',
            // Ne pas casser si la table consultations n'est pas migrée
            'consultation_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        // S'assurer que le patient appartient à l'utilisateur courant
        $patient = Patient::findOrFail($validated['patient_id']);
        if ($patient->user_id !== auth()->id()) {
            abort(403, 'Accès non autorisé');
        }

        $amount = (float) $validated['amount'];

        // Lier la consultation si possible et cohérente
        $consultationId = $validated['consultation_id'] ?? null;
        if ($consultationId && Schema::hasTable('consultations')) {
            $validConsultation = Consultation::where('id', $consultationId)
                ->where('user_id', auth()->id())
                ->where('patient_id', $patient->id)
                ->exists();
            if (! $validConsultation) {
                $consultationId = null; // ignorer un id non valide
            }
        } else {
            $consultationId = null;
        }

        // Normaliser le moyen de paiement vers les valeurs ENUM réellement présentes
        // dans la table (cash, check, card, transfer, other)
        $rawMethod = $validated['payment_method'] ?? null;
        $methodForDb = null;
        if ($rawMethod) {
            // Nettoyage: trim, lowercase, garder alphanumériques seulement
            $clean = preg_replace('/[^a-z0-9]/', '', strtolower(trim($rawMethod)));
            $map = [
                'cheque' => 'check',
                'check'  => 'check',
                'cash'   => 'cash',
                'paylib' => 'card',
                'card'   => 'card',
                'transfer' => 'transfer',
                'other'  => 'other'
            ];
            $methodForDb = $map[$clean] ?? null;
        }

        // Si on a un montant payable mais aucun mapping trouvé, utiliser 'other' pour éviter l'insert invalide
        if ($amount > 0 && empty($methodForDb)) {
            $methodForDb = 'other';
        }

        // Final method to persist
        $finalMethod = $amount > 0 ? $methodForDb : null;

    // Debug logs: request payload, validated and finalMethod (force error level to ensure log)
    \Log::error('Accounting.store payload', ['request_all' => $request->all(), 'validated' => $validated, 'finalMethod' => $finalMethod]);
        try {
            $invoice = Invoice::create([
            'patient_id' => $patient->id,
            'user_id' => auth()->id(),
            'consultation_id' => $consultationId,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date' => $validated['invoice_date'] ?? now()->toDateString(),
            'due_date' => null,
            'subtotal' => $amount,
            'tax_amount' => 0, // Non applicable ici
            'total_amount' => $amount,
            'status' => $validated['status'],
            'payment_method' => $finalMethod,
            'payment_date' => $validated['status'] === 'paid' ? (now()->toDateString()) : null,
            'notes' => $validated['notes'] ?? null,
        ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Log et renvoyer une erreur utile au JS pour debug
            \Log::error('Invoice create failed', [
                'rawMethod' => $rawMethod,
                'methodForDb' => $methodForDb,
                'exception' => $e->getMessage()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Création facture échouée',
                    'rawMethod' => $rawMethod,
                    'methodForDb' => $methodForDb,
                    'error' => $e->getMessage()
                ], 500);
            }

            abort(500, 'Création facture échouée: ' . $e->getMessage());
        }

        // Si la requête provient d'AJAX (fetch), retourner JSON; sinon redirection
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'invoice_id' => $invoice->id]);
        }

        return redirect()->route('accounting.index')->with('success', 'Facture créée avec succès.');
    }
}
