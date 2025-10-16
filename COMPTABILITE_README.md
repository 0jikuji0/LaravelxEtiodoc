# Page de Suivi de Comptabilité - EtioDoc

## 📊 Fonctionnalités

La page de comptabilité permet de :

1. **Visualiser les statistiques financières** :
   - Revenus totaux
   - Montants en attente
   - Montants en retard
   - Nombre total de factures

2. **Afficher des graphiques** :
   - Revenus mensuels (12 derniers mois)
   - Répartition par statut (payé, en attente, en retard, annulé)

3. **Filtrer les factures** :
   - Par statut
   - Par date (début et fin)
   - Par recherche (numéro de facture ou nom de patient)

4. **Gérer les factures** :
   - Voir les détails d'une facture
   - Modifier le statut de paiement
   - Mettre à jour la date et méthode de paiement
   - Imprimer ou télécharger en PDF (à implémenter)

## 🚀 Installation et Configuration

### 1. Prérequis

Assurez-vous que Docker est installé et que Laravel Sail est configuré.

### 2. Démarrer l'environnement

```bash
cd /home/jikuji/Documents/LaravelxEtiodoc
./vendor/bin/sail up -d
```

### 3. Exécuter les migrations

```bash
./vendor/bin/sail artisan migrate
```

### 4. Créer des données de test (optionnel)

```bash
./vendor/bin/sail artisan db:seed --class=InvoiceSeeder
```

## 📁 Fichiers créés

### Contrôleur
- `app/Http/Controllers/AccountingController.php`

### Vues
- `resources/views/accounting/index.blade.php` - Liste des factures avec statistiques
- `resources/views/accounting/show.blade.php` - Détails d'une facture

### Migrations
- `database/migrations/2025_10_16_*_create_invoices_table.php`
- `database/migrations/2025_10_16_*_create_invoice_items_table.php`

### Seeders
- `database/seeders/InvoiceSeeder.php`

### Routes
Routes ajoutées dans `routes/web.php` :
- `GET /accounting` - Liste des factures
- `GET /accounting/{invoice}` - Détails d'une facture
- `POST /accounting/{invoice}/update-status` - Mise à jour du statut

## 🔗 Accès

Une fois l'application démarrée :

1. Connectez-vous à l'application
2. Cliquez sur "Comptabilité" dans le menu de navigation
3. Ou accédez directement à : `http://localhost/accounting`

## 💡 Utilisation

### Filtrer les factures

1. Sélectionnez un statut dans le menu déroulant
2. Choisissez une période avec les dates de début et fin
3. Recherchez par numéro de facture ou nom de patient
4. Cliquez sur "Appliquer les filtres"

### Voir les détails d'une facture

1. Dans la liste des factures, cliquez sur "Voir"
2. Vous verrez tous les détails de la facture
3. Vous pouvez modifier le statut de paiement depuis le panneau latéral

### Mettre à jour le statut d'une facture

1. Ouvrez les détails de la facture
2. Dans le panneau "Paiement", sélectionnez le nouveau statut
3. Ajoutez la date et méthode de paiement si applicable
4. Cliquez sur "Mettre à jour"

## 🎨 Personnalisation

### Couleurs des statuts

Les statuts ont des couleurs différentes :
- **Payé** : Vert
- **En attente** : Jaune
- **En retard** : Rouge
- **Annulé** : Gris

### Graphiques

Les graphiques utilisent Chart.js et s'adaptent automatiquement au mode sombre.

## 📝 Prochaines améliorations possibles

- [ ] Génération de PDF pour les factures
- [ ] Envoi automatique par email
- [ ] Export Excel des données
- [ ] Rappels automatiques pour paiements en retard
- [ ] Tableau de bord avec prévisions de revenus
- [ ] Intégration avec système de paiement en ligne

## 🐛 Dépannage

### Erreur de base de données

Si vous rencontrez une erreur "could not find driver" :

```bash
# Assurez-vous que Docker est en cours d'exécution
./vendor/bin/sail up -d

# Vérifiez les containers
docker ps

# Exécutez les migrations avec Sail
./vendor/bin/sail artisan migrate
```

### Aucune facture n'apparaît

Si aucune facture n'apparaît, créez des données de test :

```bash
./vendor/bin/sail artisan db:seed --class=InvoiceSeeder
```

## 📧 Support

Pour toute question ou problème, consultez la documentation Laravel ou créez une issue.
