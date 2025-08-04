<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans" x-data="{ showForm: false }">

<!-- Header -->
<header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <div class="text-xl font-semibold text-gray-800">Etiodoc</div>
        <nav class="flex space-x-6">
            <a href="#" @click.prevent="showForm = true" class="text-gray-700 hover:text-blue-600">Nouveau patient</a>
            <a href="#" class="text-gray-700 hover:text-blue-600">Comptabilité</a>
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
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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

<!-- Cartes -->
<div class="px-10 mt-10">
    <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
</div>

<div class="px-6 mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-blue-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md">
        <div>
            <div class="text-4xl font-bold">{{ $patients->count() }}</div>
            <div class="text-lg">Patients enregistrés</div>
        </div>
        <div class="text-5xl">👥</div>
    </div>
    <div class="bg-green-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md">
        <div>
            <div class="text-4xl font-bold">10</div>
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

<div class="px-10 mt-10">
    <h2 class="text-xl font-semibold mb-4">Patients</h2>
    @if($patients->count())
        <ul class="space-y-2">
            @foreach($patients as $patient)
                <li class="bg-white p-4 rounded shadow flex justify-between items-center">
                    <a href="{{ route('patients.show', $patient->id) }}"
                       class="font-semibold text-gray-800 hover:text-blue-600">
                        {{ $patient->first_name }} {{ $patient->last_name }}
                    </a>
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500">{{ $patient->created_at->diffForHumans() }}</div>
                        
                        {{-- ✅ CHANGEMENT ICI : route('patients.destroy') au lieu de route('dashboard') --}}
                        <form action="{{ route('patients.destroy', $patient->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')">
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
    @else
        <p class="text-gray-500">Aucun patient enregistré.</p>
    @endif
</div>

<div x-show="showForm" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Ajouter un patient</h2>
        <form method="POST" action="{{ route('patients.store') }}">
            @csrf
            <input type="text" name="first_name" placeholder="Prénom"
                   class="w-full border border-gray-300 p-2 rounded mb-4" required>
            <input type="text" name="last_name" placeholder="Nom"
                   class="w-full border border-gray-300 p-2 rounded mb-4" required>
            <input type="text" name="phone" placeholder="phone"
                   class="w-full border border-gray-300 p-2 rounded mb-4" >
            <input type="date" name="birthdate" placeholder="birthdate"
                   class="w-full border border-gray-300 p-2 rounded mb-4" required>
            <div class="flex justify-end space-x-2">
                <button type="button" @click="showForm = false"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Annuler</button>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ajouter</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
