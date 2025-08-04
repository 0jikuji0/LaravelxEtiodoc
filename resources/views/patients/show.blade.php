<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans p-6">

<a href="{{ route('dashboard') }}" class="text-blue-600 mb-6 inline-block">&larr; Retour au tableau de bord</a>

<div class="bg-white p-6 rounded shadow max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Détails du patient</h1>
    <p><strong>Prénom :</strong> {{ $patient->first_name }}</p>
    <p><strong>Nom :</strong> {{ $patient->last_name }}</p>
    <p><strong>Téléphone :</strong> {{ $patient->phone }}</p>
    <p><strong>Anniv :</strong> {{ $patient->birthdate }}</p>

    <p class="mt-4 text-sm text-gray-500">Créé le : {{ $patient->created_at->format('d/m/Y à H:i') }}</p>
</div>

</body>
</html>
