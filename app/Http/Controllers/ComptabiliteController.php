<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComptabiliteController extends Controller
{
    public function index()
    {
        return view('comptabilite');
    }

    public function getStats()
    {
        $user_id = auth()->id();
        
        // Statistiques générales
        $stats = [
            'totalRevenue' => 0,
            'revenueGrowth' => 0,
            'totalConsultations' => 0,
            'consultationsThisMonth' => 0,
            'consultationsGrowth' => 0,
            'freeActs' => 0,
            'freeActsPercentage' => 0
        ];

        // Chiffre d'affaires total
        $totalRevenue = Consultation::where('user_id', $user_id)
            ->where('payment_status', 'paid')
            ->where('act_type', 'payant')
            ->sum('price');
        
        $stats['totalRevenue'] = $totalRevenue;

        // Nombre total de consultations
        $totalConsultations = Consultation::where('user_id', $user_id)->count();
        $stats['totalConsultations'] = $totalConsultations;

        // Consultations ce mois
        $currentMonth = Carbon::now()->startOfMonth();
        $consultationsThisMonth = Consultation::where('user_id', $user_id)
            ->whereBetween('consultation_date', [$currentMonth, Carbon::now()])
            ->count();
        $stats['consultationsThisMonth'] = $consultationsThisMonth;

        // Actes gratuits
        $freeActs = Consultation::where('user_id', $user_id)
            ->where('act_type', 'gratuit')
            ->count();
        $stats['freeActs'] = $freeActs;
        $stats['freeActsPercentage'] = $totalConsultations > 0 ? round(($freeActs / $totalConsultations) * 100, 1) : 0;

        // Comparaison avec le mois précédent
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthRevenue = Consultation::where('user_id', $user_id)
            ->where('payment_status', 'paid')
            ->where('act_type', 'payant')
            ->whereBetween('payment_date', [$currentMonth, Carbon::now()])
            ->sum('price');

        $lastMonthRevenue = Consultation::where('user_id', $user_id)
            ->where('payment_status', 'paid')
            ->where('act_type', 'payant')
            ->whereBetween('payment_date', [$lastMonth, $currentMonth])
            ->sum('price');

        $stats['revenueGrowth'] = $lastMonthRevenue > 0 ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        // Croissance des consultations
        $lastMonthConsultations = Consultation::where('user_id', $user_id)
            ->whereBetween('consultation_date', [$lastMonth, $currentMonth])
            ->count();
        
        $stats['consultationsGrowth'] = $lastMonthConsultations > 0 ? round((($consultationsThisMonth - $lastMonthConsultations) / $lastMonthConsultations) * 100, 1) : 0;

        return response()->json($stats);
    }

    public function getTransactions(Request $request)
    {
        $user_id = auth()->id();
        
        $query = Consultation::where('consultations.user_id', $user_id)
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->select([
                'consultations.id',
                'consultations.consultation_date',
                'consultations.payment_date',
                'consultations.price',
                'consultations.payment_method',
                'consultations.payment_status',
                'consultations.act_type',
                'consultations.payment_notes',
                'patients.first_name',
                'patients.last_name'
            ]);

        // Filtres
        if ($request->filled('period')) {
            $this->applyPeriodFilter($query, $request->period);
        }

        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('consultations.payment_method', $request->payment_method);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('consultations.payment_status', $request->status);
        }

        if ($request->filled('min_amount') && $request->min_amount > 0) {
            $query->where('consultations.price', '>=', $request->min_amount);
        }

        $transactions = $query->orderBy('consultations.payment_date', 'desc')
            ->get()
            ->map(function ($consultation) {
                return [
                    'id' => $consultation->id,
                    'patient_name' => $consultation->first_name . ' ' . $consultation->last_name,
                    'consultation_id' => $consultation->id,
                    'amount' => $consultation->act_type === 'gratuit' ? 0 : $consultation->price,
                    'payment_method' => $consultation->payment_method,
                    'payment_status' => $consultation->payment_status,
                    'payment_date' => $consultation->payment_date,
                    'notes' => $consultation->payment_notes
                ];
            });

        return response()->json($transactions);
    }

    public function getRevenueChart()
    {
        $user_id = auth()->id();
        
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $revenue = Consultation::where('user_id', $user_id)
                ->where('payment_status', 'paid')
                ->where('act_type', 'payant')
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->sum('price');

            $months[] = [
                'month' => $date->format('M'),
                'amount' => $revenue
            ];
        }

        return response()->json($months);
    }

    public function getPaymentMethods()
    {
        $user_id = auth()->id();
        
        $methods = Consultation::where('user_id', $user_id)
            ->whereNotNull('payment_method')
            ->select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        $total = $methods->sum('count');
        
        $paymentMethods = $methods->map(function ($method) use ($total) {
            $colors = [
                'especes' => 'bg-green-500',
                'cheque' => 'bg-blue-500',
                'paylib' => 'bg-purple-500',
                'gratuit' => 'bg-gray-500'
            ];

            return [
                'name' => $this->getPaymentMethodName($method->payment_method),
                'percentage' => $total > 0 ? round(($method->count / $total) * 100, 1) : 0,
                'color' => $colors[$method->payment_method] ?? 'bg-gray-500'
            ];
        });

        return response()->json($paymentMethods);
    }

    private function applyPeriodFilter($query, $period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                $query->whereDate('consultations.payment_date', $now->toDateString());
                break;
            case 'week':
                $query->whereBetween('consultations.payment_date', [
                    $now->startOfWeek(),
                    $now->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('consultations.payment_date', [
                    $now->startOfMonth(),
                    $now->endOfMonth()
                ]);
                break;
            case 'quarter':
                $query->whereBetween('consultations.payment_date', [
                    $now->startOfQuarter(),
                    $now->endOfQuarter()
                ]);
                break;
            case 'year':
                $query->whereBetween('consultations.payment_date', [
                    $now->startOfYear(),
                    $now->endOfYear()
                ]);
                break;
        }
    }

    private function getPaymentMethodName($method)
    {
        $names = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'paylib' => 'PayLib',
            'gratuit' => 'Gratuit'
        ];

        return $names[$method] ?? $method;
    }

    public function export(Request $request)
    {
        // Ici vous pourriez implémenter l'export des données
        // Par exemple, générer un fichier Excel ou CSV
        
        return response()->json([
            'message' => 'Export en cours de développement'
        ]);
    }
}
