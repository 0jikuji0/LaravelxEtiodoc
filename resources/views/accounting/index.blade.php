<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Comptabilité - Etiodoc</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">
  @include('layouts.topbar')

  <!-- Page container -->
  <main class="max-w-7xl mx-auto mt-6 px-4">
    <!-- Header row -->
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-bold">Comptabilité</h2>
      <button id="btn-filtres" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg shadow-sm">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 5h18M6 12h12M10 19h4"/></svg>
        Filtres
      </button>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <!-- CA -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs uppercase text-gray-500">Chiffre d'affaires</p>
            <p class="mt-2 text-2xl font-semibold">{{ number_format($stats['total_revenue'] ?? 0, 2, ',', ' ') }} €</p>
            <p class="mt-1 text-xs {{ isset($stats['revenue_delta']) && $stats['revenue_delta'] < 0 ? 'text-red-600' : 'text-green-600' }}">
              {{ isset($stats['revenue_delta']) ? $stats['revenue_delta'] : 0 }}% vs mois dernier
            </p>
          </div>
          <span class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">$</span>
        </div>
      </div>

      <!-- Consultations -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs uppercase text-gray-500">Consultations</p>
            <p class="mt-2 text-2xl font-semibold">{{ $stats['total_consultations'] ?? 0 }}</p>
            @php
              $consDelta = (($stats['consultations_last_month'] ?? 0) > 0)
                ? round((($stats['consultations_this_month'] ?? 0) - ($stats['consultations_last_month'] ?? 0)) / max(1, ($stats['consultations_last_month'] ?? 1)) * 100)
                : 0;
            @endphp
            <p class="mt-1 text-xs {{ $consDelta < 0 ? 'text-red-600' : 'text-blue-600' }}">{{ $consDelta }}% vs mois dernier</p>
          </div>
          <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg>
          </span>
        </div>
      </div>

      <!-- Consultations ce mois -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs uppercase text-gray-500">Consultations ce mois</p>
            <p class="mt-2 text-2xl font-semibold">{{ $stats['consultations_this_month'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-amber-600">{{ $consDelta }}% vs mois dernier</p>
          </div>
          <span class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 0 0 2-2v-6H3v6a2 2 0 0 0 2 2Z"/></svg>
          </span>
        </div>
      </div>

      <!-- Actes gratuits -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs uppercase text-gray-500">Actes gratuits</p>
            <p class="mt-2 text-2xl font-semibold">{{ $stats['free_acts'] ?? 0 }}</p>
            <p class="mt-1 text-xs text-purple-600">{{ $stats['free_acts_percent'] ?? 0 }}% du total des consultations</p>
          </div>
          <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">$</span>
        </div>
      </div>
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
      <!-- Revenus -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <h3 class="text-sm font-semibold mb-2">Évolution des revenus</h3>
        <canvas id="revenusChart" height="120"></canvas>
      </div>
      <!-- Méthodes de paiement -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <h3 class="text-sm font-semibold mb-3">Répartition par méthode de paiement</h3>
        <ul class="space-y-3">
          @php
            $labelMap = ['cash' => 'Espèces', 'cheque' => 'Chèque', 'paylib' => 'Paylib'];
            $totals = $paymentMethodTotals ?? collect();
            $percents = $paymentMethodPercents ?? collect();
          @endphp
          @forelse($totals as $method => $total)
            @php $pct = $percents[$method] ?? 0; @endphp
            <li>
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm text-gray-700">{{ $labelMap[$method] ?? ucfirst($method) }}</span>
                <span class="text-xs text-gray-500">{{ $pct }}%</span>
              </div>
              <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-2 bg-blue-600" style="width: {{ $pct }}%"></div>
              </div>
            </li>
          @empty
            <li class="text-sm text-gray-500">Aucun paiement enregistré</li>
          @endforelse
        </ul>
      </div>
    </div>

    <!-- Ajout rapide facture/consultation -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">Ajout rapide</h3>
      <form action="{{ route('accounting.store') }}" method="POST" class="grid md:grid-cols-4 gap-4 items-end">
        @csrf
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Patient</label>
          <select name="patient_id" class="w-full border rounded-lg px-3 py-2" required>
            <option value="" disabled selected>Choisir un patient…</option>
            @foreach(($patients ?? []) as $p)
              <option value="{{ $p->id }}">{{ $p->last_name }} {{ $p->first_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Tarif</label>
          <select name="amount" class="w-full border rounded-lg px-3 py-2">
            @foreach(($presetTariffs ?? [35,40,45,50]) as $t)
              <option value="{{ $t }}" {{ $t==45 ? 'selected' : '' }}>{{ $t }} €</option>
            @endforeach
            <option value="0">Gratuit</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Méthode de paiement</label>
          <select name="payment_method" class="w-full border rounded-lg px-3 py-2">
            <option value="cheque">Chèque</option>
            <option value="cash">Espèces</option>
            <option value="paylib">Paylib</option>
          </select>
        </div>
        <input type="hidden" name="status" value="paid">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Ajouter</button>
      </form>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-semibold">Transactions récentes</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500">
              <th class="py-2 pr-4">Date</th>
              <th class="py-2 pr-4">Patient</th>
              <th class="py-2 pr-4">Consultation</th>
              <th class="py-2 pr-4">Montant</th>
              <th class="py-2 pr-4">Méthode</th>
              <th class="py-2 pr-4">Statut</th>
              <th class="py-2">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @php
              $statusLabel = ['paid'=>'Payé','pending'=>'En attente','overdue'=>'En retard','cancelled'=>'Annulée'];
              $pmLabel = ['cash'=>'Espèces','cheque'=>'Chèque','paylib'=>'Paylib'];
            @endphp
            @forelse(($recentInvoices ?? []) as $inv)
              <tr>
                <td class="py-2 pr-4">{{ optional($inv->invoice_date)->format('d/m/Y') ?? (is_string($inv->invoice_date) ? \Carbon\Carbon::parse($inv->invoice_date)->format('d/m/Y') : '') }}</td>
                <td class="py-2 pr-4">{{ $inv->patient?->last_name }} {{ $inv->patient?->first_name }}</td>
                <td class="py-2 pr-4">{{ $inv->consultation_id ? ('Consultation #' . $inv->consultation_id) : '—' }}</td>
                <td class="py-2 pr-4">
                  @if(($inv->total_amount ?? 0) > 0)
                    <span class="text-emerald-600 font-semibold">{{ number_format($inv->total_amount, 2, ',', ' ') }} €</span>
                  @else
                    <span class="text-gray-500">Gratuit</span>
                  @endif
                </td>
                <td class="py-2 pr-4">
                  @if(($inv->total_amount ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $pmLabel[$inv->payment_method] ?? ucfirst($inv->payment_method ?? '—') }}</span>
                  @else
                    <span class="text-gray-400">—</span>
                  @endif
                </td>
                <td class="py-2 pr-4">
                  @php
                    $st = $inv->status ?? 'pending';
                    $color = $st==='paid' ? 'bg-emerald-100 text-emerald-700' : ($st==='pending' ? 'bg-amber-100 text-amber-700' : ($st==='overdue' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'));
                  @endphp
                  <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $color }}">{{ $statusLabel[$st] ?? ucfirst($st) }}</span>
                </td>
                <td class="py-2">
                  <a href="{{ route('accounting.show', $inv->id) }}" class="text-blue-600 hover:text-blue-700">Voir</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-6 text-center text-gray-500">Aucune transaction récente</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script>
    // Chart from server data
    (() => {
      const ctx = document.getElementById('revenusChart');
      const breakdown = @json(($monthlyBreakdown ?? collect())->map(fn($m) => [
        'month' => $m['month'] ?? (is_object($m) ? ($m->month ?? '') : ''),
        'revenue' => (float)($m['revenue'] ?? (is_object($m) ? ($m->revenue ?? 0) : 0))
      ]));
      // Prepare last 6 months labels and data
      const months = [];
      const values = [];
      const now = new Date();
      const toKey = (d) => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`;
      const toLabel = (d) => d.toLocaleDateString('fr-FR', { month:'short' });
      const revenueMap = {};
      (breakdown || []).forEach(m => { if(m && m.month){ revenueMap[m.month] = m.revenue || 0; }});
      for(let i=5;i>=0;i--){
        const d = new Date(now.getFullYear(), now.getMonth()-i, 1);
        months.push(toLabel(d));
        values.push((revenueMap[toKey(d)] || 0).toFixed(2));
      }
      new Chart(ctx, {
        type: 'bar',
        data: { labels: months, datasets: [{
          label: 'Revenus (€)',
          data: values,
          backgroundColor: 'rgba(59, 130, 246, 0.6)',
          borderColor: 'rgb(59, 130, 246)',
          borderWidth: 1,
          borderRadius: 6,
        }]},
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: { beginAtZero: true, ticks: { callback: v => v + ' €' } },
            x: { grid: { display: false } }
          }
        }
      });
    })();
  </script>
</body>
</html>
