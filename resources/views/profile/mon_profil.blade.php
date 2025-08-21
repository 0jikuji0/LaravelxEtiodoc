<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mon - Compte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    
    {{-- Header --}}
    <header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-6">
            <div class="text-xl font-semibold text-gray-800">
                <a href="{{ route('dashboard') }}">
              Etiodoc
          </a>
            </div>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" @click.away="open = false"
            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50"
            style="display:none" x-cloak>
            <a href="{{ route('profile') }}"
            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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

{{-- Page Profil --}}
<main class="max-w-4xl mx-auto py-10 px-6">
    <div class="bg-white shadow rounded-xl p-8">
        <div class="flex items-center space-x-6">
            <img src="https://via.placeholder.com/120"
            class="w-28 h-28 rounded-full shadow-md" alt="Photo de profil">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ Auth::user()->name }}</h1>
                <p class="text-gray-600">{{ Auth::user()->email }}</p>
                <p class="mt-2 text-gray-700">Développeur web passionné par Laravel, Tailwind et les projets innovants 🚀</p>
            </div>
        </div>
        
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Compétences</h2>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>Laravel & PHP</li>
                    <li>JavaScript (Vue, Alpine, etc.)</li>
                    <li>HTML / CSS / Tailwind</li>
                    <li>SQL & Bases de données</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Informations</h2>
                <p class="text-gray-700"><span cl<button x-show="consultation.payment_status === 'pending'" 
                                                @click="updatePaymentStatus(consultation.id, 'paid')"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Marquer payé
                                        </button>ass="font-semibold">Localisation :</span> Paris, France</p>
                <p class="text-gray-700"><span class="font-semibold">Statut :</span> Étudiant ESGI & Développeur</p>
                <p class="text-gray-700"><span class="font-semibold">Passion :</span> Tech, IA, et projets web</p>
            </div>
        </div>
    </div>
</main>

</body>
</html>