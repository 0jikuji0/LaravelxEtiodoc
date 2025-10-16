<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tableau de bord</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<!-- Header -->
<header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
  <!-- Logo + Liens -->
  <div class="flex items-center space-x-6">
    <div class="text-xl font-semibold text-gray-800">
      Elodoc
    </div>
    <nav class="flex space-x-6">
      <a href="#" class="text-gray-700 hover:text-blue-600">Nouveau patient</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Comptabilité</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Contact</a>
    </nav>
  </div>

  <!-- deco + recherche à faire  -->
<div x-data="{ open: false }" class="relative">
  <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
    <span class="mr-2">{{ Auth::user()->name }}</span>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
  </button>

  <!-- Menu déroulant -->
  <div x-show="open" @click.away="open = false"
       class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50">
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


  <!-- Dashboard Title -->
  <div class="px-10 mt-10">
    <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
  </div>

  <!-- Boutons du haut -->
  <div class="px-6 mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Nouveau patient -->
    <div class="bg-blue-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md">
      <div>
        <div class="text-4xl font-bold">0</div>
        <div class="text-lg">Nouveaux patients</div>
      </div>
      <div class="text-5xl">➕</div>
    </div>

    <!-- Consultations -->
    <div class="bg-green-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md">
      <div>
        <div class="text-4xl font-bold">10</div>
        <div class="text-lg">Consultations</div>
      </div>
      <div class="text-5xl">📁</div>
    </div>

    <!-- Retours -->
    <div class="bg-red-500 text-white rounded-lg p-6 flex items-center justify-between shadow-md">
      <div>
        <div class="text-4xl font-bold">0</div>
        <div class="text-lg">Retours</div>
      </div>
      <div class="text-5xl">⚠️</div>
    </div>
  </div>

</body>
<footer>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</footer>
</html>
