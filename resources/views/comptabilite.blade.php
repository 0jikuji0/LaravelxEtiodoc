<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Comptabilité - Etiodoc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen font-sans" x-data="comptabilite()">

<!-- Header -->
<header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <div class="text-xl font-semibold text-gray-800">
           <a href="{{ route('dashboard') }}">
              Etiodoc
          </a>
        </div>
        <nav class="flex space-x-6">
            <a href="#" @click.prevent="showNewPatientForm = true" class="text-gray-700 hover:text-blue-600">Nouveau patient</a>
            <a href="{{ route('comptabilite') }}" class="text-blue-600 font-medium">Comptabilité</a>
            <a href="#" class="text-gray-700 hover:text-blue-600">Contact</a>
        </nav>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Menu utilisateur -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
                <span class="mr-2">Dr. Martin</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" @click.away="open = false"
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50" style="display:none" x-cloak>
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Mon compte
                </a>
                <form method="POST" action="{{ route('logout') }}">
                @csrf
                    <button type="submit" 
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- Contenu principal -->
<div class="px-6 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Comptabilité</h1>
        <div class="flex space-x-3">
            <button @click="showFilters = !showFilters" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                Filtres
            </button>
        </div>
    </div>

    <!-- Filtres -->
    <div x-show="showFilters" x-transition class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtres</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                <select x-model="filters.period" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">Toutes les périodes</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="quarter">Ce trimestre</option>
                    <option value="year">Cette année</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Méthode de paiement</label>
                <select x-model="filters.paymentMethod" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">Toutes</option>
                    <option value="especes">Espèces</option>
                    <option value="cheque">Chèque</option>
                    <option value="paylib">PayLib</option>
                    <option value="gratuit">Gratuit</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select x-model="filters.status" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all">Tous</option>
                    <option value="paid">Payé</option>
                    <option value="pending">En attente</option>
                    <option value="cancelled">Annulé</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Montant minimum</label>
                <input type="number" x-model="filters.minAmount" min="0" step="0.01"
                       class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="0€">
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Chiffre d'affaires total -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Chiffre d'affaires</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.totalRevenue)"></p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-green-600 font-medium" x-text="'+' + stats.revenueGrowth + '%'"></span>
                <span class="text-sm text-gray-600">vs mois dernier</span>
            </div>
        </div>

        <!-- Consultations -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Consultations</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.totalConsultations"></p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-blue-600 font-medium" x-text="stats.consultationGrowth + '%'"></span>
                <span class="text-sm text-gray-600">vs mois dernier</span>
            </div>
        </div>

        <!-- Consultations ce mois -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Consultations ce mois</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.consultationsThisMonth"></p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-yellow-600 font-medium" x-text="stats.consultationsGrowth + '%'"></span>
                <span class="text-sm text-gray-600">vs mois dernier</span>
            </div>
        </div>

        <!-- Actes gratuits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Actes gratuits</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.freeActs"></p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-purple-600 font-medium" x-text="stats.freeActsPercentage + '%'"></span>
                <span class="text-sm text-gray-600">du total des consultations</span>
            </div>
        </div>
    </div>

    <!-- Graphiques et analyses -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique des revenus -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Évolution des revenus</h3>
            <div class="h-64 flex items-end justify-between space-x-2">
                <template x-for="(month, index) in revenueChart" :key="index">
                    <div class="flex-1 flex flex-col items-center">
                        <div class="bg-blue-500 rounded-t w-full" 
                             :style="'height: ' + (month.amount / Math.max(...revenueChart.map(m => m.amount)) * 200) + 'px'">
                        </div>
                        <span class="text-xs text-gray-600 mt-2" x-text="month.month"></span>
                        <span class="text-xs font-medium" x-text="formatCurrency(month.amount)"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Répartition par méthode de paiement -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition par méthode de paiement</h3>
            <div class="space-y-4">
                <template x-for="method in paymentMethods" :key="method.name">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 rounded-full" :class="method.color"></div>
                            <span class="text-sm font-medium text-gray-700" x-text="method.name"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" :style="'width: ' + method.percentage + '%'"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900" x-text="method.percentage + '%'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Tableau des transactions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Transactions récentes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Patient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Consultation
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Montant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Méthode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="transaction in filteredTransactions" :key="transaction.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatDate(transaction.payment_date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="transaction.patient_name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="'Consultation #' + transaction.consultation_id"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" 
                                :class="transaction.amount > 0 ? 'text-green-600' : 'text-gray-500'"
                                x-text="transaction.amount > 0 ? formatCurrency(transaction.amount) : 'Gratuit'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getPaymentMethodClass(transaction.payment_method)">
                                    <span x-text="getPaymentMethodText(transaction.payment_method)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="getStatusClass(transaction.payment_status)">
                                    <span x-text="getStatusText(transaction.payment_status)"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="viewTransaction(transaction)" 
                                        class="text-blue-600 hover:text-blue-900">Voir</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Affichage de <span x-text="(currentPage - 1) * itemsPerPage + 1"></span> à 
                    <span x-text="Math.min(currentPage * itemsPerPage, filteredTransactions.length)"></span> 
                    sur <span x-text="filteredTransactions.length"></span> résultats
                </div>
                <div class="flex space-x-2">
                    <button @click="currentPage = Math.max(1, currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                        Précédent
                    </button>
                    <button @click="currentPage = Math.min(totalPages, currentPage + 1)" 
                            :disabled="currentPage === totalPages"
                            class="px-3 py-1 text-sm border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                        Suivant
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de détail transaction -->
<div x-show="showTransactionModal" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Détails de la transaction</h2>
            <button @click="showTransactionModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div x-show="selectedTransaction" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Patient</label>
                    <p class="text-sm text-gray-900" x-text="selectedTransaction.patient_name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date de paiement</label>
                    <p class="text-sm text-gray-900" x-text="formatDateTime(selectedTransaction.payment_date)"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Montant</label>
                    <p class="text-sm font-medium text-gray-900" x-text="selectedTransaction.amount > 0 ? formatCurrency(selectedTransaction.amount) : 'Gratuit'"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Méthode de paiement</label>
                    <p class="text-sm text-gray-900" x-text="getPaymentMethodText(selectedTransaction.payment_method)"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusClass(selectedTransaction.payment_status)">
                        <span x-text="getStatusText(selectedTransaction.payment_status)"></span>
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Consultation ID</label>
                    <p class="text-sm text-gray-900" x-text="selectedTransaction.consultation_id"></p>
                </div>
            </div>
            
            <div x-show="selectedTransaction.notes">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <p class="text-sm text-gray-900" x-text="selectedTransaction.notes"></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau patient -->
<div x-show="showNewPatientForm" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Ajouter un patient</h2>
            <button @click="showNewPatientForm = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form @submit.prevent="addPatient()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                    <input type="text" x-model="newPatient.firstName" required
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Prénom du patient">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                    <input type="text" x-model="newPatient.lastName" required
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nom du patient">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" x-model="newPatient.phone"
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Numéro de téléphone">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                    <input type="date" x-model="newPatient.birthdate" required
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="showNewPatientForm = false"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function comptabilite() {
    return {
        showFilters: false,
        showTransactionModal: false,
        showNewPatientForm: false,
        selectedTransaction: null,
        currentPage: 1,
        itemsPerPage: 10,
        
        filters: {
            period: 'all',
            paymentMethod: 'all',
            status: 'all',
            minAmount: 0
        },
        
        // Statistiques
        stats: {
            totalRevenue: 12500,
            revenueGrowth: 12.5,
            totalConsultations: 156,
            consultationsThisMonth: 23,
            consultationsGrowth: 8.3,
            freeActs: 8,
            freeActsPercentage: 5.1
        },
        
        // Graphique des revenus
        revenueChart: [
            { month: 'Jan', amount: 8500 },
            { month: 'Fév', amount: 9200 },
            { month: 'Mar', amount: 7800 },
            { month: 'Avr', amount: 10200 },
            { month: 'Mai', amount: 11500 },
            { month: 'Juin', amount: 12500 }
        ],
        
        // Méthodes de paiement
        paymentMethods: [
            { name: 'Espèces', percentage: 45, color: 'bg-green-500' },
            { name: 'Chèque', percentage: 30, color: 'bg-blue-500' },
            { name: 'PayLib', percentage: 20, color: 'bg-purple-500' },
            { name: 'Gratuit', percentage: 5, color: 'bg-gray-500' }
        ],
        
        // Transactions (données simulées)
        transactions: [
            {
                id: 1,
                patient_name: 'MARTINS Lilo',
                consultation_id: 123,
                amount: 45,
                payment_method: 'especes',
                payment_status: 'paid',
                payment_date: '2025-01-15 14:30:00',
                notes: 'Paiement en espèces'
            },
            {
                id: 2,
                patient_name: 'DUPONT Marie',
                consultation_id: 124,
                amount: 50,
                payment_method: 'cheque',
                payment_status: 'paid',
                payment_date: '2025-01-15 16:45:00',
                notes: 'Chèque reçu'
            },
            {
                id: 3,
                patient_name: 'MARTIN Paul',
                consultation_id: 125,
                amount: 0,
                payment_method: 'gratuit',
                payment_status: 'paid',
                payment_date: '2025-01-16 09:15:00',
                notes: 'Acte gratuit - pas d\'impact comptable'
            },
            {
                id: 4,
                patient_name: 'DURAND Jean',
                consultation_id: 126,
                amount: 40,
                payment_method: 'paylib',
                payment_status: 'paid',
                payment_date: '2025-01-16 11:20:00',
                notes: 'Paiement PayLib'
            }
        ],
        
        init() {
            this.loadData();
        },
        
        async loadData() {
            try {
                // Charger les statistiques
                const statsResponse = await fetch('/api/comptabilite/stats');
                if (statsResponse.ok) {
                    this.stats = await statsResponse.json();
                }
                
                // Charger les transactions
                const transactionsResponse = await fetch('/api/comptabilite/transactions');
                if (transactionsResponse.ok) {
                    this.transactions = await transactionsResponse.json();
                }
                
                // Charger le graphique des revenus
                const chartResponse = await fetch('/api/comptabilite/revenue-chart');
                if (chartResponse.ok) {
                    this.revenueChart = await chartResponse.json();
                }
                
                // Charger les méthodes de paiement
                const methodsResponse = await fetch('/api/comptabilite/payment-methods');
                if (methodsResponse.ok) {
                    this.paymentMethods = await methodsResponse.json();
                }
                
                console.log('Données de comptabilité chargées');
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
            }
        },
        
        get filteredTransactions() {
            let filtered = this.transactions;
            
            // Filtre par méthode de paiement
            if (this.filters.paymentMethod !== 'all') {
                filtered = filtered.filter(t => t.payment_method === this.filters.paymentMethod);
            }
            
            // Filtre par statut
            if (this.filters.status !== 'all') {
                filtered = filtered.filter(t => t.payment_status === this.filters.status);
            }
            
            // Filtre par montant minimum
            if (this.filters.minAmount > 0) {
                filtered = filtered.filter(t => t.amount >= this.filters.minAmount);
            }
            
            // Filtre par période
            if (this.filters.period !== 'all') {
                const now = new Date();
                let startDate;
                
                switch (this.filters.period) {
                    case 'today':
                        startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                        break;
                    case 'week':
                        startDate = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                        break;
                    case 'month':
                        startDate = new Date(now.getFullYear(), now.getMonth(), 1);
                        break;
                    case 'quarter':
                        startDate = new Date(now.getFullYear(), Math.floor(now.getMonth() / 3) * 3, 1);
                        break;
                    case 'year':
                        startDate = new Date(now.getFullYear(), 0, 1);
                        break;
                }
                
                if (startDate) {
                    filtered = filtered.filter(t => new Date(t.payment_date) >= startDate);
                }
            }
            
            return filtered;
        },
        
        get totalPages() {
            return Math.ceil(this.filteredTransactions.length / this.itemsPerPage);
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('fr-FR');
        },
        
        formatDateTime(dateString) {
            return new Date(dateString).toLocaleString('fr-FR');
        },
        
        getPaymentMethodText(method) {
            const methods = {
                'especes': 'Espèces',
                'cheque': 'Chèque',
                'paylib': 'PayLib',
                'gratuit': 'Gratuit'
            };
            return methods[method] || method;
        },
        
        getPaymentMethodClass(method) {
            const classes = {
                'especes': 'bg-green-100 text-green-800',
                'cheque': 'bg-blue-100 text-blue-800',
                'paylib': 'bg-purple-100 text-purple-800',
                'gratuit': 'bg-gray-100 text-gray-800'
            };
            return classes[method] || 'bg-gray-100 text-gray-800';
        },
        
        getStatusText(status) {
            const statuses = {
                'paid': 'Payé',
                'pending': 'En attente',
                'cancelled': 'Annulé',
                'gratuit': 'Gratuit'
            };
            return statuses[status] || status;
        },
        
        getStatusClass(status) {
            const classes = {
                'paid': 'bg-green-100 text-green-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'cancelled': 'bg-red-100 text-red-800',
                'gratuit': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        viewTransaction(transaction) {
            this.selectedTransaction = transaction;
            this.showTransactionModal = true;
        },
        
        // Données pour le nouveau patient
        newPatient: {
            firstName: '',
            lastName: '',
            phone: '',
            birthdate: ''
        },
        
        async addPatient() {
            try {
                const response = await fetch('/patients', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        first_name: this.newPatient.firstName,
                        last_name: this.newPatient.lastName,
                        phone: this.newPatient.phone,
                        birthdate: this.newPatient.birthdate
                    })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Réinitialiser le formulaire
                    this.newPatient = {
                        firstName: '',
                        lastName: '',
                        phone: '',
                        birthdate: ''
                    };
                    
                    // Fermer le modal
                    this.showNewPatientForm = false;
                    
                    alert('Patient ajouté avec succès !');
                    
                    // Optionnel: rediriger vers le nouveau patient
                    if (result.patient_id) {
                        window.location.href = `/patients/${result.patient_id}`;
                    }
                } else {
                    throw new Error('Erreur lors de la création du patient');
                }
                
            } catch (error) {
                console.error('Erreur lors de la création du patient:', error);
                alert('Erreur lors de la création du patient: ' + error.message);
            }
        }
    }
}
</script>

</body>
</html> 