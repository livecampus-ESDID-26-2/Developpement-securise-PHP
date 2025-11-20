# 💰 Système de Caisse Enregistreuse - Développement Sécurisé PHP

**École :** LiveCampus - ESDID-26.2  
**Étudiant :** MASIA Antoine

## 📋 Description du Projet

Application PHP de gestion de caisse enregistreuse avec authentification multi-utilisateurs permettant de :
- **Système d'authentification** : Login sécurisé avec gestion des rôles (utilisateur/administrateur)
- **Calcul automatique** : Calculer automatiquement la monnaie à rendre
- **Algorithmes multiples** : Optimiser le rendu (algorithme glouton standard ou inversé)
- **Gestion personnalisée** : Chaque utilisateur gère sa propre caisse
- **Historique détaillé** : Suivi complet des transactions par utilisateur
- **Dashboard Admin** : Vue d'ensemble de tous les utilisateurs et leurs activités
- **Affichage visuel** : Interface moderne avec images réelles de billets et pièces

## 🏗️ Architecture du Projet

### Architecture MVC (Model-View-Controller)

Le projet suit une architecture MVC orientée objet moderne avec autoloading PSR-4 :

```
app/
├── index.php                          # Front Controller - Point d'entrée unique
├── bootstrap.php                      # Initialisation de l'application
├── routes.php                         # Définition des routes
│
├── Core/                              # Classes de base du framework
│   ├── Autoloader.php                 # Autoloader PSR-4
│   ├── Database.php                   # Singleton de connexion PDO
│   ├── Router.php                     # Système de routage
│   ├── Controller.php                 # Contrôleur de base
│   ├── Model.php                      # Modèle de base
│   └── Session.php                    # Gestion des sessions
│
├── Models/                            # Modèles (Logique métier et accès données)
│   ├── User.php                       # Modèle utilisateur (authentification)
│   ├── CashRegister.php               # Modèle caisse (état, calculs)
│   ├── Transaction.php                # Modèle transaction (historique)
│   └── Currency.php                   # Configuration des billets/pièces
│
├── Controllers/                       # Contrôleurs (Logique applicative)
│   ├── AuthController.php             # Authentification (login/logout)
│   ├── CashRegisterController.php     # Gestion de la caisse (transactions)
│   └── AdminController.php            # Administration (dashboard, stats)
│
└── views/                             # Vues (Interface utilisateur)
    ├── login.php                      # Page de connexion
    ├── cash_register_form.php         # Formulaire de saisie caisse
    ├── cash_register_result.php       # Affichage des résultats
    ├── history.php                    # Historique utilisateur
    ├── admin/                         # Vues administrateur
    │   ├── dashboard.php              # Dashboard admin
    │   ├── history.php                # Historique global
    │   └── user_detail.php            # Détail utilisateur
    └── style.css                      # Styles CSS (1150+ lignes)

database/
└── init.sql                           # Script d'initialisation de la BDD
```

### Flux de l'application MVC

```
Requête HTTP
     ↓
index.php (Front Controller)
     ↓
bootstrap.php (Initialisation + Autoloader PSR-4)
     ↓
Router (Analyse URL → Trouve la route)
     ↓
Controller (Logique applicative)
     ↓
Model (Accès aux données BDD)
     ↓
View (Affichage HTML)
     ↓
Réponse HTTP
```

**Caractéristiques :**
- ✅ Point d'entrée unique (`index.php`)
- ✅ Autoloading PSR-4 automatique
- ✅ URLs propres sans `.php`
- ✅ Séparation stricte des responsabilités

## ✨ Fonctionnalités

### 🔐 Système d'Authentification
- **Login sécurisé** : Page de connexion avec validation des identifiants
- **Gestion des sessions** : Sessions PHP sécurisées avec vérification automatique
- **Rôles utilisateurs** : 
  - 👤 **Utilisateur** : Accès à sa caisse personnelle et son historique
  - 👨‍💼 **Administrateur** : Vue d'ensemble de tous les utilisateurs et leurs activités
- **Middleware** : Protection automatique des pages selon les droits d'accès
- **Déconnexion** : Bouton de déconnexion sur toutes les pages

### 💵 Calcul de Monnaie
- **Algorithme glouton (standard)** : Optimise le nombre de billets/pièces à rendre (du plus grand au plus petit)
- **Algorithme inversé** : Rendu de monnaie du plus petit au plus grand
- **Valeur préférée** : Option pour privilégier une dénomination spécifique (ex: maximiser les pièces de 1€)
- **Validation** : Vérifie la disponibilité en caisse
- **Précision** : Calculs en centimes pour éviter les erreurs de flottants

### 🎨 Interface Utilisateur
- **Design moderne** : Interface responsive avec dégradés et animations
- **Images réelles** : Billets et pièces d'euros officiels de la BCE
- **Badges visuels** : Mise en évidence de la monnaie à rendre
- **Codes couleurs** :
  - 🔵 Bleu/Violet : Interface utilisateur standard
  - 🟠 Orange : Dashboard administrateur
  - 🟢 Vert : Entrées d'argent
  - 🔴 Rouge : Sorties d'argent
- **Responsive** : Compatible desktop, tablette et mobile

### 📊 Gestion de Caisse (Utilisateur)
- **Caisse personnelle** : Chaque utilisateur gère sa propre caisse
- **État initial** : Affichage de la caisse avant transaction
- **Nouvel état** : Affichage après transaction avec différences
- **Comparaison** : Vue avant/après côte à côte
- **Persistance** : Sauvegarde automatique de l'état de la caisse en base de données
- **Historique personnel** : 
  - Aperçu des 5 dernières transactions sur la page principale
  - Page dédiée avec l'historique complet de l'utilisateur
  - Détails visuels avec images des billets/pièces rendus
  - Statistiques (nombre de transactions, total rendu)

### 👨‍💼 Dashboard Administrateur
- **Vue d'ensemble** : Liste de tous les utilisateurs (hors admins) avec leurs statistiques
- **Statistiques globales** : 
  - Nombre total d'utilisateurs
  - Nombre total de transactions
  - Total des montants rendus
- **Par utilisateur** :
  - Nombre de transactions effectuées
  - 3 dernières transactions en aperçu
  - Accès au détail complet
- **Historique global** : Vue de toutes les transactions de tous les utilisateurs
- **Détail utilisateur** : Historique complet et statistiques d'un utilisateur spécifique

## 🚀 Installation et Utilisation

### Prérequis
- Docker
- Docker Compose

### Configuration

1. **Cloner le projet** :
```bash
git clone https://github.com/livecampus-ESDID-26-2/Developpement-securise-PHP
cd Developpement-securise-PHP
```

2. **Configurer les variables d'environnement** :
```bash
# Copier le fichier d'exemple
cp env.exemple .env

# Éditer le fichier .env si nécessaire
# Par défaut, les valeurs sont déjà configurées pour Docker
```

Le fichier `.env` contient les paramètres de connexion à la base de données :
```env
DB_HOST=db              # Nom du service Docker (ne pas modifier)
DB_PORT=3306            # Port MySQL
DB_NAME=cash            # Nom de la base de données
DB_USER=root            # Utilisateur MySQL
DB_PASSWORD=rootpassword # Mot de passe MySQL (à modifier en production !)
```

⚠️ **Important** : Le fichier `.env` est ignoré par Git pour des raisons de sécurité. Ne jamais commit ce fichier avec des identifiants réels.

### Démarrage

3. **Lancer Docker Compose** :
```bash
docker compose up
```

4. **Attendre l'initialisation** :
   La première fois, Docker va :
   - Construire l'image PHP avec les extensions PDO MySQL
   - Télécharger l'image MySQL
   - Initialiser la base de données avec le script `database/init.sql`
   - Cela peut prendre quelques minutes

5. **Accéder à l'application** :
   Ouvrir le navigateur à l'adresse : http://localhost:8000
   
   Vous serez redirigé vers la page de connexion.

6. **Arrêter le serveur** :
```bash
# Ctrl+C dans le terminal, puis :
docker compose down
```

## 🔧 Technologies Utilisées

### Backend
- **PHP 8.4** : POO avancée avec namespaces PSR-4
- **MySQL 8.0** : Base de données relationnelle
- **PDO** : Couche d'abstraction avec requêtes préparées

### Architecture
- **MVC** : Pattern Model-View-Controller
- **PSR-4** : Autoloading automatique des classes
- **Singleton** : Pattern pour la connexion BDD
- **Front Controller** : Point d'entrée unique
- **Routing** : URLs propres et RESTful

### Frontend
- **HTML5/CSS3** : Interface responsive
- **Design moderne** : Dégradés, animations, responsive

### Infrastructure
- **Docker** : Conteneurisation complète
- **Apache** : Serveur web avec mod_rewrite

## 🔒 Sécurité

✅ **Authentification** : Système de login avec sessions PHP sécurisées  
✅ **Gestion des rôles** : Middleware pour protéger les pages selon les droits d'accès  
✅ **Injections SQL** : Protection via requêtes préparées PDO  
✅ **Injections XSS** : Échappement des données avec htmlspecialchars()  
✅ **Typage strict** : Validation et typage des données (intval(), floatval())  
✅ **Méthodes HTTP** : Vérification POST uniquement pour les formulaires  
✅ **Variables d'environnement** : Identifiants sensibles dans fichier .env  
✅ **Contrôle de version** : Fichier .env exclu de Git via .gitignore  
✅ **Gestion des erreurs** : Logging côté serveur (error_log)  
✅ **Sessions sécurisées** : Démarrage automatique et destruction propre

⚠️ **Note** : Les mots de passe en base de données sont en clair pour la démonstration. 
En production, utiliser `password_hash()` et `password_verify()`.

## 📝 Configuration

### Autoloading PSR-4

L'application utilise un autoloader conforme PSR-4. Plus besoin de `require_once` !

```php
// Les classes se chargent automatiquement
use App\Models\User;
use App\Controllers\CashRegisterController;

$user = new User(); // Chargé depuis app/Models/User.php
```

### Namespaces

```php
App\Core\*          → app/Core/
App\Models\*        → app/Models/
App\Controllers\*   → app/Controllers/
```

### Base de Données

**Tables créées automatiquement** :
- `users` : Utilisateurs du système avec rôles (user/admin)
  - Colonnes : id, email, password, role, created_at
- `cash_register_state` : État de la caisse à chaque transaction
  - Contient tous les billets et pièces (15 colonnes)
  - Le dernier enregistrement = état actuel de la caisse
- `transaction_history` : Historique complet des transactions
  - Stocke : montants, algorithme, valeur préférée, user_id
  - JSON : change_returned, register_before, register_after
  - Permet le filtrage par utilisateur

**Utilisateurs de démonstration** :
| Email | Mot de passe | Rôle | Accès |
|-------|--------------|------|-------|
| `user1@cash.com` | `12345` | 👤 Utilisateur | Caisse + Historique personnel |
| `user2@cash.com` | `12345` | 👤 Utilisateur | Caisse + Historique personnel |
| `admin@cash.com` | `123456` | 👨‍💼 Admin | Dashboard + Vue d'ensemble |

**État initial de la caisse** :
- 1×500€, 2×200€, 2×100€, 4×50€, 1×20€, 23×10€, 0×5€
- 34×2€, 23×1€, 23×0.50€, 80×0.20€, 12×0.10€, 8×0.05€, 45×0.02€, 12×0.01€

## 🎓 Projet Pédagogique

Ce projet fait partie du module "**Développement Sécurisé PHP**" à **LiveCampus - ESDID-26.2** et démontre :

### Compétences techniques

#### Architecture & Patterns
- ✅ **MVC** : Séparation Model-View-Controller
- ✅ **POO avancée** : Classes abstraites, héritage, namespaces PSR-4
- ✅ **Design Patterns** : Singleton, Front Controller, MVC
- ✅ **SOLID** : Principes de conception orientée objet
- ✅ **Autoloading** : PSR-4 avec chargement automatique

#### Développement PHP
- ✅ **PHP 8.4** : Typage strict, nouvelles fonctionnalités
- ✅ **Sécurité** : Protection XSS, SQL injection, CSRF
- ✅ **Base de données** : MySQL avec PDO et requêtes préparées
- ✅ **Sessions** : Gestion sécurisée de l'authentification
- ✅ **Routing** : URLs propres et RESTful

#### Algorithmique
- ✅ **Algorithmes de rendu** : Glouton, inversé, avec préférence
- ✅ **Optimisation** : Calcul optimal de la monnaie

#### DevOps
- ✅ **Docker** : Conteneurisation multi-services
- ✅ **Git** : Versioning et branches

### Fonctionnalités avancées
- 🔐 Système d'authentification multi-utilisateurs
- 👥 Gestion des rôles (utilisateur/administrateur)
- 📊 Historique avec filtrage par utilisateur
- 💾 Persistance des données en base
- 🎨 Interface moderne et responsive
- 📈 Dashboard administrateur avec statistiques

### Bonnes pratiques
- **Code structuré** : Architecture MVC claire et maintenable
- **POO** : Programmation orientée objet avec namespaces
- **PSR-4** : Autoloading standardisé des classes
- **Separation of Concerns** : Séparation logique/présentation/données
- **DRY** : Don't Repeat Yourself - Réutilisation du code
- **Variables d'environnement** : Configuration sensible externalisée
- **Gestion des erreurs** : Logging et gestion des exceptions
- **Validation des données** : Typage et validation stricte
- **Design moderne** : Interface responsive et UX soignée
