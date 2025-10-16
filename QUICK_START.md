# 🚀 Guide de démarrage rapide - Comptabilité EtioDoc

## ⚠️ Problème actuel : Base de données

L'erreur `could not find driver` indique que MySQL n'est pas connecté. Voici comment résoudre ce problème :

## 🔧 Solution : Utiliser Laravel Sail (Docker)

### Étape 1 : Vérifier que Docker est installé

```bash
docker --version
```

Si Docker n'est pas installé, installez-le d'abord.

### Étape 2 : Démarrer les conteneurs Docker

```bash
cd /home/jikuji/Documents/LaravelxEtiodoc
./vendor/bin/sail up -d
```

**Note :** La première fois, cela peut prendre quelques minutes pour télécharger les images Docker.

### Étape 3 : Vérifier que les conteneurs sont actifs

```bash
./vendor/bin/sail ps
```

Vous devriez voir des conteneurs en cours d'exécution (mysql, php, etc.)

### Étape 4 : Exécuter les migrations

```bash
./vendor/bin/sail artisan migrate
```

Cette commande créera toutes les tables nécessaires, y compris :
- `invoices` (factures)
- `invoice_items` (lignes de factures)
- `patients`
- `users`
- etc.

### Étape 5 : Créer des données de test

```bash
./vendor/bin/sail artisan db:seed --class=InvoiceSeeder
```

Cela créera 20 factures fictives pour tester la page de comptabilité.

### Étape 6 : Accéder à l'application

Ouvrez votre navigateur et allez à : **http://localhost**

## 🔄 Alternative : Utiliser SQLite (plus simple)

Si vous ne voulez pas utiliser Docker, vous pouvez utiliser SQLite :

### Étape 1 : Modifier le fichier .env

```bash
nano .env
```

Changez :
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

Par :
```env
DB_CONNECTION=sqlite
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=sail
# DB_PASSWORD=password
```

### Étape 2 : Créer le fichier de base de données

```bash
touch database/database.sqlite
```

### Étape 3 : Exécuter les migrations

```bash
php artisan migrate
```

### Étape 4 : Créer des données de test

```bash
php artisan db:seed --class=InvoiceSeeder
```

### Étape 5 : Démarrer le serveur

```bash
php artisan serve
```

Puis ouvrez : **http://localhost:8000**

## 📊 Accès à la page de comptabilité

Une fois l'application démarrée :

1. **Connectez-vous** avec vos identifiants
2. **Cliquez sur "Comptabilité"** dans le menu de navigation
3. Vous verrez :
   - 📈 Statistiques (revenus, factures en attente, etc.)
   - 📊 Graphiques (revenus mensuels, répartition par statut)
   - 🔍 Filtres pour rechercher des factures
   - 📋 Liste de toutes les factures

## 🛠️ Commandes utiles

### Avec Docker (Sail)

```bash
# Démarrer les conteneurs
./vendor/bin/sail up -d

# Arrêter les conteneurs
./vendor/bin/sail down

# Voir les logs
./vendor/bin/sail logs

# Accéder au container
./vendor/bin/sail shell

# Exécuter des commandes artisan
./vendor/bin/sail artisan [commande]

# Réinitialiser la base de données
./vendor/bin/sail artisan migrate:fresh --seed
```

### Sans Docker

```bash
# Démarrer le serveur
php artisan serve

# Exécuter les migrations
php artisan migrate

# Créer des données de test
php artisan db:seed --class=InvoiceSeeder

# Réinitialiser la base de données
php artisan migrate:fresh --seed
```

## 🎯 Vérification

Pour vérifier que tout fonctionne :

1. ✅ Les conteneurs Docker sont actifs (ou SQLite est configuré)
2. ✅ Les migrations ont été exécutées sans erreur
3. ✅ Vous pouvez vous connecter à l'application
4. ✅ Le menu "Comptabilité" apparaît dans la navigation
5. ✅ La page `/accounting` s'affiche correctement

## 🆘 Aide supplémentaire

### Problème : "SQLSTATE[HY000] [2002] Connection refused"

**Solution :** Les conteneurs Docker ne sont pas démarrés
```bash
./vendor/bin/sail up -d
```

### Problème : "could not find driver"

**Solution :** Utilisez Sail ou SQLite comme indiqué ci-dessus

### Problème : Aucune facture n'apparaît

**Solution :** Exécutez le seeder
```bash
./vendor/bin/sail artisan db:seed --class=InvoiceSeeder
# OU
php artisan db:seed --class=InvoiceSeeder
```

## 📞 Support

Si vous rencontrez des problèmes, vérifiez :
1. Les logs de Laravel : `storage/logs/laravel.log`
2. Les logs Docker : `./vendor/bin/sail logs`
3. La configuration `.env`

Bonne utilisation de la page de comptabilité ! 🎉
