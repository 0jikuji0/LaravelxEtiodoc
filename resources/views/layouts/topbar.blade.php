<!-- Unified Topbar (Dashboard-style) -->
<header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
    <div class="flex items-center space-x-6">
        <div class="text-xl font-semibold text-gray-800">
            <a href="{{ route('dashboard') }}">Etiodoc</a>
        </div>
        <nav class="flex space-x-6">
            <a id="new-patient-link" href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600">Nouveau patient</a>
            <a href="{{ route('accounting.index') }}" class="text-gray-700 hover:text-blue-600">Comptabilité</a>
            <a href="{{ route('contacts.index') }}" class="text-gray-700 hover:text-blue-600">Contacts</a>
        </nav>
    </div>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
            <span class="mr-2">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
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