<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Comptabilité - Elidoc</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">
  @include('layouts.topbar')

  <main class="max-w-6xl mx-auto mt-8 px-4">
    <h2 class="text-2xl font-bold mb-4">Comptabilité</h2>

    <!-- Statistiques -->
    <div class="grid md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white p-4 rounded-xl shadow text-center">
        <h3 class="text-gray-500 text-sm">Chiffre d'affaires</h3>
        <p id="chiffre-affaires" class="text-2xl font-semibold mt-2">0,00 €</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow text-center">
        <h3 class="text-gray-500 text-sm">Consultations</h3>
        <p id="nb-consultations" class="text-2xl font-semibold mt-2">0</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow text-center">
        <h3 class="text-gray-500 text-sm">Actes gratuits</h3>
        <p id="nb-gratuits" class="text-2xl font-semibold mt-2">0</p>
      </div>
      <div class="bg-white p-4 rounded-xl shadow text-center">
        <h3 class="text-gray-500 text-sm">Méthode la plus utilisée</h3>
        <p id="top-methode" class="text-2xl font-semibold mt-2 text-blue-600">—</p>
      </div>
    </div>

    <!-- Formulaire ajout consultation -->
    <div class="bg-white p-6 rounded-xl shadow mb-8">
      <h3 class="text-lg font-semibold mb-4">Nouvelle consultation</h3>
      <div class="grid md:grid-cols-4 gap-4 items-end">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Nom du patient</label>
          <input id="patient" type="text" class="w-full border rounded-lg px-3 py-2" placeholder="Ex: Lilo MARTINS">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Tarif</label>
          <select id="tarif" class="w-full border rounded-lg px-3 py-2">
            <option value="35">35 €</option>
            <option value="40">40 €</option>
            <option value="45" selected>45 €</option>
            <option value="50">50 €</option>
            <option value="0">Gratuit</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Méthode de paiement</label>
          <select id="methode" class="w-full border rounded-lg px-3 py-2">
            <option value="Chèque">Chèque</option>
            <option value="Espèces">Espèces</option>
            <option value="Paylib">Paylib</option>
          </select>
        </div>
        <button id="ajouter" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
          Ajouter
        </button>
      </div>
    </div>

    <!-- Tableau des transactions -->
    <div class="bg-white p-6 rounded-xl shadow">
      <h3 class="text-lg font-semibold mb-4">Transactions récentes</h3>
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-100 text-left text-sm">
            <th class="p-3">Date</th>
            <th class="p-3">Patient</th>
            <th class="p-3">Montant</th>
            <th class="p-3">Méthode</th>
            <th class="p-3">Statut</th>
          </tr>
        </thead>
        <tbody id="table-transactions"></tbody>
      </table>
    </div>
  </main>

  <script>
    const transactions = [];
    const tableBody = document.getElementById("table-transactions");
    const chiffreAffairesEl = document.getElementById("chiffre-affaires");
    const nbConsultationsEl = document.getElementById("nb-consultations");
    const nbGratuitsEl = document.getElementById("nb-gratuits");
    const topMethodeEl = document.getElementById("top-methode");

    document.getElementById("ajouter").addEventListener("click", () => {
      const patient = document.getElementById("patient").value.trim();
      const tarif = parseFloat(document.getElementById("tarif").value);
      const methode = document.getElementById("methode").value;

      if (!patient) {
        alert("Veuillez entrer un nom de patient.");
        return;
      }

      const date = new Date().toLocaleDateString("fr-FR");
      const statut = tarif > 0 ? "Payé" : "Gratuit";
      transactions.push({ date, patient, tarif, methode, statut });

      majTableau();
      majStats();

      document.getElementById("patient").value = "";
    });

    function majTableau() {
      tableBody.innerHTML = transactions.map(t => `
        <tr class="border-b text-sm">
          <td class="p-3">${t.date}</td>
          <td class="p-3">${t.patient}</td>
          <td class="p-3">${t.tarif > 0 ? t.tarif.toFixed(2) + " €" : "Gratuit"}</td>
          <td class="p-3">${t.tarif > 0 ? t.methode : "-"}</td>
          <td class="p-3">
            <span class="${t.tarif > 0 ? 'text-green-600' : 'text-gray-500'} font-medium">${t.statut}</span>
          </td>
        </tr>
      `).join("");
    }

    function majStats() {
      const total = transactions.filter(t => t.tarif > 0).reduce((acc, t) => acc + t.tarif, 0);
      const nbConsultations = transactions.length;
      const nbGratuits = transactions.filter(t => t.tarif === 0).length;
      const paiementCount = {};

      transactions.forEach(t => {
        if (t.tarif > 0) paiementCount[t.methode] = (paiementCount[t.methode] || 0) + 1;
      });

      const topMethode = Object.entries(paiementCount).sort((a, b) => b[1] - a[1])[0]?.[0] || "—";

      chiffreAffairesEl.textContent = total.toFixed(2) + " €";
      nbConsultationsEl.textContent = nbConsultations;
      nbGratuitsEl.textContent = nbGratuits;
      topMethodeEl.textContent = topMethode;
    }
  </script>
</body>
</html>
