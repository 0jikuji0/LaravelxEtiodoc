<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Contacts - Etiodoc</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  @include('layouts.topbar')

  <main class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Contacts</h1>
      <a href="{{ route('patients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Nouveau patient</a>
    </div>

    <div class="bg-white shadow rounded-lg p-4">
      <div class="mb-4">
        <input id="q" placeholder="Rechercher par nom, téléphone ou email" class="w-full border rounded px-3 py-2" />
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500">
              <th class="py-2 pr-4">Nom</th>
              <th class="py-2 pr-4">Téléphone</th>
              <th class="py-2 pr-4">Email</th>
              <th class="py-2 pr-4">Naissance</th>
              <th class="py-2 pr-4">Actions</th>
            </tr>
          </thead>
          <tbody id="contacts-list" class="divide-y divide-gray-100">
            @foreach($patients as $p)
            <tr>
              <td class="py-3 pr-4">{{ $p->last_name }} {{ $p->first_name }}</td>
              <td class="py-3 pr-4">{{ $p->phone ?? '—' }}</td>
              <td class="py-3 pr-4">{{ $p->email ?? '—' }}</td>
              <td class="py-3 pr-4">{{ $p->birthdate?->format('d/m/Y') ?? '—' }}</td>
              <td class="py-3 pr-4">
                <a href="{{ route('patients.show', $p->id) }}" class="text-blue-600 hover:underline mr-3">Voir</a>
                <a href="{{ route('patients.edit', $p->id) }}" class="text-gray-600 hover:underline">Éditer</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script>
    // simple client-side search
    const q = document.getElementById('q');
    const rows = Array.from(document.querySelectorAll('#contacts-list tr'));
    q.addEventListener('input', () => {
      const val = q.value.trim().toLowerCase();
      rows.forEach(r => {
        r.style.display = (!val) ? '' : (r.textContent.toLowerCase().includes(val) ? '' : 'none');
      });
    });
  </script>
</body>
</html>
