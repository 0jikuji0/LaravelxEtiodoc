<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans" x-data="patientManager()">

<!-- Header -->
<header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <div class="text-xl font-semibold text-gray-800">Etiodoc</div>
        <nav class="flex space-x-6">
            <a href="#" @click.prevent="showForm = true" class="text-gray-700 hover:text-blue-600">Nouveau patient</a>
            <a href="{{ route('accounting.index') }}" class="text-gray-700 hover:text-blue-600">Comptabilité</a>
            <a href="#" class="text-gray-700 hover:text-blue-600">Contact</a>
        </nav>
    </div>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
            <span class="mr-2">{{ Auth::user()->name }}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div x-show="open" @click.away="open = false"
             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50" style="display:none" x-cloak>
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
</header>

<!-- Messages flash -->
@if(session('success'))
    <div class="mx-6 mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-green-800">{{ session('success') }}</span>
        </div>
    </div>
@endif

<!-- Cartes -->
<div class="px-10 mt-10">
    <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
</div>

<div class="px-6 mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('patients.list') }}" 
   class="bg-blue-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md cursor-pointer">
    <div>
        <div class="text-4xl font-bold">{{ $patients->count() }}</div>
        <div class="text-lg">Patients enregistrés</div>
    </div>
    <div class="text-5xl">👥</div>
</a>
    <div class="bg-green-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md">
        <div>
            <div class="text-4xl font-bold">{{ $patients->sum(function($patient) { return $patient->consultations->count(); }) }}</div>
            <div class="text-lg">Consultations</div>
        </div>
        <div class="text-5xl">📁</div>
    </div>
    <div class="bg-red-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md">
        <div>
            <div class="text-4xl font-bold">0</div>
            <div class="text-lg">Retours</div>
        </div>
        <div class="text-5xl">⚠️</div>
    </div>
</div>

<!-- Section Patients avec recherche et tri -->
<div class="px-10 mt-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Patients</h2>
        <div class="flex items-center space-x-4">
            <!-- Barre de recherche -->
            <div class="relative">
                <input 
                    type="text" 
                    x-model="searchTerm"
                    @input="filterPatients()"
                    placeholder="Rechercher un patient..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            
            <!-- Sélecteur de type de tri -->
            <select 
                x-model="sortType"
                @change="sortPatients()"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
            >
                <option value="name">Trier par nom</option>
                <option value="date">Trier par date</option>
            </select>
            
            <!-- Bouton de direction du tri -->
            <button 
                @click="toggleSortDirection()"
                class="flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors duration-200"
                :title="getSortTitle()"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          :d="sortDirection === 'asc' ? 'M3 4h13M3 8h9M3 12h9m7-4v8m-4-4l4-4m0 0l4 4' : 'M3 4h13M3 8h9M3 12h5m7-4v8m-4-4l4 4m0 0l4-4'" />
                </svg>
                <span x-text="getSortLabel()"></span>
            </button>
        </div>
    </div>
    
    <!-- Compteur de résultats -->
    <div class="mb-4">
        <span class="text-sm text-gray-600">
            <span x-text="visibleCount"></span> patient(s) affiché(s)
            <span x-show="searchTerm !== ''" x-text="'pour \"' + searchTerm + '\"'"></span>
        </span>
    </div>

    @if($patients->count())
        <ul class="space-y-2" id="patients-container">
            @foreach($patients as $patient)
                <li class="bg-white p-4 rounded shadow flex justify-between items-center hover:shadow-md transition-shadow duration-200 patient-item"
                    data-patient-name="{{ strtolower($patient->first_name . ' ' . $patient->last_name) }}"
                    data-patient-date="{{ $patient->created_at->timestamp }}">
                    
                    <!-- LIEN VERS LA PAGE PATIENT MODIFIÉ -->
                    <a href="{{ route('patients.show', $patient->id) }}"
                       class="font-semibold text-gray-800 hover:text-blue-600 transition-colors duration-200 flex-1">
                        {{ $patient->first_name }} {{ $patient->last_name }}
                        <span class="text-sm text-gray-500 ml-2">
                            - {{ \Carbon\Carbon::parse($patient->birthdate)->age }} ans
                            @if($patient->phone)
                                - {{ $patient->phone }}
                            @endif
                        </span>
                    </a>

                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500">{{ $patient->created_at->diffForHumans() }}</div>
                        
                        <!-- Bouton Voir détails -->
                        <a href="{{ route('patients.show', $patient->id) }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-sm transition duration-200">
                            Voir
                        </a>
                        
                        <form action="{{ route('patients.destroy', $patient->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ? Cette action supprimera aussi toutes ses consultations et antécédents.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm transition duration-200">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
        
        <!-- Message quand aucun résultat trouvé -->
        <div x-show="visibleCount === 0 && searchTerm !== ''" class="text-center py-8">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <p class="text-gray-500">Aucun patient trouvé pour "<span x-text="searchTerm"></span>"</p>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun patient enregistré</h3>
            <p class="text-gray-500 mb-4">Commencez par ajouter votre premier patient.</p>
            <button @click="showForm = true" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                Ajouter un patient
            </button>
        </div>
    @endif
</div>

<!-- Modal Nouveau patient MODIFIÉ AVEC PLUS DE CHAMPS -->
<div x-show="showForm" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-lg font-bold mb-4">Ajouter un patient</h2>
        <form method="POST" action="{{ route('patients.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                    <input type="text" name="first_name" placeholder="Prénom"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="last_name" placeholder="Nom"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" name="phone" placeholder="Téléphone"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance *</label>
                    <input type="date" name="birthdate"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" placeholder="Email"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                    <input type="text" name="city" placeholder="Ville"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code postal</label>
                    <input type="text" name="postal_code" placeholder="Code postal"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact d'urgence</label>
                    <input type="text" name="emergency_contact_name" placeholder="Nom du contact d'urgence"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                    <textarea name="address" placeholder="Adresse complète" rows="3"
                              class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone d'urgence</label>
                    <input type="tel" name="emergency_contact_phone" placeholder="Téléphone d'urgence"
                           class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" @click="showForm = false"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Annuler
                </button>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Ajouter le patient
                </button>
            </div>
        </form>
    </div>
</div>

<script>

function patientManager() {

return {

showForm: false,

searchTerm: '',

sortType: 'name', // 'name' ou 'date'

sortDirection: 'asc', // 'asc' ou 'desc'

visibleCount: 0,

init() {

this.updateVisibleCount();

 },

filterPatients() {

const patients = document.querySelectorAll('.patient-item');

let count = 0;

patients.forEach(patient => {

const patientName = patient.dataset.patientName;

const searchLower = this.searchTerm.toLowerCase();

if (patientName.includes(searchLower)) {

patient.style.display = 'flex';

count++;

 } else {

patient.style.display = 'none';

 }

 });

this.visibleCount = count;

this.sortPatients();

 },

toggleSortDirection() {

this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';

this.sortPatients();

 },

getSortTitle() {

if (this.sortType === 'name') {

return this.sortDirection === 'asc' ? 'Trier de A à Z' : 'Trier de Z à A';

 } else {

return this.sortDirection === 'asc' ? 'Trier du plus ancien au plus récent' : 'Trier du plus récent au plus ancien';

 }

 },

getSortLabel() {

if (this.sortType === 'name') {

return this.sortDirection === 'asc' ? 'A-Z' : 'Z-A';

 } else {

return this.sortDirection === 'asc' ? '↑ Ancien' : '↓ Récent';

 }

 },

sortPatients() {

const container = document.getElementById('patients-container');

const patients = Array.from(container.querySelectorAll('.patient-item')).filter(item =>

item.style.display !== 'none'

 );

patients.sort((a, b) => {

let compareValue = 0;

if (this.sortType === 'name') {

const nameA = a.dataset.patientName;

const nameB = b.dataset.patientName;

compareValue = nameA.localeCompare(nameB);

 } else if (this.sortType === 'date') {

const dateA = parseInt(a.dataset.patientDate);

const dateB = parseInt(b.dataset.patientDate);

compareValue = dateA - dateB;

 }

return this.sortDirection === 'asc' ? compareValue : -compareValue;

 });

// Réorganiser les éléments dans le DOM

patients.forEach(patient => {

container.appendChild(patient);

 });

 },

updateVisibleCount() {

const patients = document.querySelectorAll('.patient-item');

this.visibleCount = patients.length;

 }

 }

}

</script>
</body>
</html>