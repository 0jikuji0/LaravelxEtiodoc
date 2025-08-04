<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Nouvelle Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

<!-- Header -->
<header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
  <!-- Logo + Liens -->
  <div class="flex items-center space-x-6">
    <div class="text-xl font-semibold text-blue-700">
      MyApp
    </div>
    <nav class="flex space-x-6">
      <a href="#" class="text-gray-700 hover:text-blue-600">Accueil</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Fonctionnalité</a>
      <a href="#" class="text-gray-700 hover:text-blue-600">Contact</a>
    </nav>
  </div>

  <!-- User Dropdown -->
  <div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none">
      <span class="mr-2">Utilisateur</span>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <div x-show="open" @click.away="open = false"
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50">
      <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
        Mon compte
      </a>
      <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
        Paramètres
      </a>
      <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
        Déconnexion
      </a>
    </div>
  </div>
</header>

<!-- Main Content -->
<div class="px-10 mt-10">
  <h1 class="text-2xl font-bold text-gray-800">Bienvenue sur la nouvelle page</h1>
  <p class="mt-4 text-gray-600">Ajoutez ici le contenu principal de votre nouvelle page.</p>
</div>

<footer>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</footer>
</body>
</html> 