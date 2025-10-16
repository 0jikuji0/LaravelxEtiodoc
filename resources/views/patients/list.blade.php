<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des patients - EtioDoc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

@include('layouts.topbar')

<!-- Liste des patients -->
<div class="px-10 mt-10">
    @if($patients->count())
        <ul class="space-y-2">
            @foreach($patients as $patient)
                <li class="bg-white p-4 rounded shadow flex justify-between items-center hover:shadow-md transition">
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



</body>
</html>
