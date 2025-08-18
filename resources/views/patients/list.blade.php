<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des patients</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    <!-- Header -->
    <header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        <!-- Titre -->
        <h1 class="text-xl font-bold text-gray-800">Liste des patients</h1>

        <!-- Recherche + Tri + Retour -->
        <div class="flex items-center space-x-4">
            <!-- Recherche -->
            <div class="flex items-center border rounded-lg shadow-sm px-2 py-1 bg-white">
                <!-- Icône loupe -->
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 flex-shrink-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                </svg>

                <!-- Input -->
                <input type="text" id="searchInput"
                    placeholder="Rechercher..."
                    class="ml-2 outline-none flex-1 text-sm bg-transparent">
            </div>


            <!-- Tri -->
            <select id="sortSelect"
                    class="px-3 py-2 border rounded-lg shadow-sm focus:ring focus:ring-blue-200 text-sm">
                <option value="default">Trier</option>
                <option value="name-asc">Nom (A → Z)</option>
                <option value="name-desc">Nom (Z → A)</option>
                <option value="age-asc">Âge (croissant)</option>
                <option value="age-desc">Âge (décroissant)</option>
            </select>

            <!-- Retour bouton -->
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm">
                Retour
            </a>
        </div>
    </header>

    <!-- Liste des patients -->
    <div class="px-10 mt-10">
        @if($patients->count())
            <ul id="patientsList" class="space-y-2">
                @foreach($patients as $patient)
                    <li class="patient-item bg-white p-4 rounded shadow flex justify-between items-center hover:shadow-md transition"
                        data-name="{{ strtolower($patient->first_name . ' ' . $patient->last_name) }}"
                        data-age="{{ \Carbon\Carbon::parse($patient->birthdate)->age }}">

                        <!-- Nom + infos -->
                        <a href="{{ route('patients.show', $patient->id) }}"
                           class="font-semibold text-gray-800 hover:text-blue-600 transition flex-1">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                            <span class="text-sm text-gray-500 ml-2">
                                - {{ \Carbon\Carbon::parse($patient->birthdate)->age }} ans
                                @if($patient->phone)
                                    - {{ $patient->phone }}
                                @endif
                            </span>
                        </a>

                        <!-- Actions -->
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('patients.show', $patient->id) }}"
                               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-sm">
                                Voir
                            </a>
                            <form action="{{ route('patients.destroy', $patient->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Supprimer ce patient ? Toutes ses données seront perdues.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun patient trouvé</h3>
                <p class="text-gray-500">Ajoutez un patient depuis le tableau de bord.</p>
            </div>
        @endif
    </div>

    @extends('layouts.app')

    <!-- Script JS -->
    <script>
        const searchInput = document.getElementById('searchInput');
        const sortSelect = document.getElementById('sortSelect');
        const patientsList = document.getElementById('patientsList');
        const patients = Array.from(patientsList?.children || []);

        // Recherche
        searchInput?.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            patients.forEach(patient => {
                const name = patient.dataset.name;
                patient.style.display = name.includes(query) ? '' : 'none';
            });
        });

        // Tri
        sortSelect?.addEventListener('change', () => {
            let sorted = [...patients];
            const value = sortSelect.value;

            if (value === 'name-asc') {
                sorted.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
            } else if (value === 'name-desc') {
                sorted.sort((a, b) => b.dataset.name.localeCompare(a.dataset.name));
            } else if (value === 'age-asc') {
                sorted.sort((a, b) => a.dataset.age - b.dataset.age);
            } else if (value === 'age-desc') {
                sorted.sort((a, b) => b.dataset.age - a.dataset.age);
            }

            patientsList.innerHTML = '';
            sorted.forEach(patient => patientsList.appendChild(patient));
        });
    </script>

</body>
</html>
