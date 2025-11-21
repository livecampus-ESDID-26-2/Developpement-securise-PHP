# Système de Caisse Enregistreuse - Développement Sécurisé PHP

**École :** LiveCampus - ESDID-26.2  
**Étudiant :** [Antoine MASIA](https://github.com/MasiaAntoine) - Full-Stack Developer  
**Intervenant :** [Alexandre Herbeth](https://github.com/Aherbeth) - Fullstack Developer & Trainer

---

<div align="center">

<table>
  <tr">
    <td align="center" width="50%"">
      <a href="https://github.com/MasiaAntoine">
        <img src="https://avatars.githubusercontent.com/u/115811899?v=4" alt="Antoine MASIA" width="100">
      </a>
      <br/>
      <strong>Antoine MASIA</strong>
      <br/>
      <em>Étudiant</em>
    </td>
    <td align="center" width="50%"">
      <a href="https://github.com/Aherbeth">
        <img src="https://avatars.githubusercontent.com/u/17410092" alt="Alexandre Herbeth" 
        width="100">      
      </a>
      <br/>
      <strong>Alexandre Herbeth</strong>
      <br/>
      <em>Intervenant</em>
    </td>

  </tr>
</table>

<br>

<em>Projet réalisé dans le cadre du module "Développement Sécurisé PHP"</em>

</div>

---

## Description du Projet

Application PHP de gestion de caisse enregistreuse avec authentification multi-utilisateurs permettant de :

- **Système d'authentification** : Login sécurisé avec gestion des rôles (utilisateur/administrateur)
- **Calcul automatique** : Calculer automatiquement la monnaie à rendre
- **Algorithmes multiples** : Optimiser le rendu (algorithme glouton standard ou inversé)
- **Gestion personnalisée** : Chaque utilisateur gère sa propre caisse
- **Système de facturation** : Génération et envoi de factures multi-formats (Email, Courrier, Impression, SMS)
- **Historique détaillé** : Suivi complet des transactions par utilisateur avec factures associées
- **Dashboard Admin** : Vue d'ensemble de tous les utilisateurs et leurs activités
- **Affichage visuel** : Interface moderne avec images réelles de billets et pièces

## Sommaire

- [Description du Projet](#description-du-projet)
- [Captures d'écran](#captures-décran)
- [Architecture du Projet](#architecture-du-projet)
  - [Architecture de Sécurité](#architecture-de-sécurité)
  - [Architecture MVC](#architecture-mvc-model-view-controller)
  - [Flux de l'application MVC](#flux-de-lapplication-mvc)
- [Fonctionnalités](#fonctionnalités)
  - [Système d'Authentification](#système-dauthentification)
  - [Calcul de Monnaie](#calcul-de-monnaie)
  - [Interface Utilisateur](#interface-utilisateur)
  - [Gestion de Caisse (Utilisateur)](#gestion-de-caisse-utilisateur)
  - [Système de Facturation](#système-de-facturation)
  - [Dashboard Administrateur](#dashboard-administrateur)
- [Installation et Utilisation](#installation-et-utilisation)
  - [Prérequis](#prérequis)
  - [Configuration](#configuration)
  - [Démarrage](#démarrage)
  - [Réinitialisation de la base de données](#réinitialisation-de-la-base-de-données)
- [Technologies Utilisées](#technologies-utilisées)
- [Sécurité](#sécurité)
- [Configuration](#configuration-1)
- [Projet Pédagogique](#projet-pédagogique)

## Captures d'écran

### Page de connexion

![Page de connexion](/docs/screenshots/login.png)

_Interface de connexion sécurisée avec gestion des rôles utilisateur/administrateur_

### Interface de Caisse - Formulaire de Transaction

![Formulaire de caisse](/docs/screenshots/cash-register-form.png)

_Formulaire de saisie avec montant dû, montant reçu et choix de l'algorithme de rendu_

### Résultat de Transaction - Affichage de la Monnaie

![Résultat de transaction](/docs/screenshots/cash-register-result.png)

_Affichage visuel de la monnaie à rendre avec images réelles des billets et pièces, état de la caisse avant/après_

### Historique Utilisateur

![Historique utilisateur](/docs/screenshots/user-history.png)

_Historique complet des transactions de l'utilisateur avec détails visuels et statistiques_

### Facture Utilisateur

![Facture utilisateur](/docs/screenshots/user-invoice.png)

_Exemple de Facture de l'utilisateur avec détails visuels_

### Dashboard Administrateur

![Dashboard admin](/docs/screenshots/admin-dashboard.png)

_Vue d'ensemble de tous les utilisateurs avec statistiques globales et accès rapide aux détails_

### Historique Global (Admin)

![Historique global](/docs/screenshots/admin-history.png)

_Vue complète de toutes les transactions effectuées par tous les utilisateurs_

### Détail Utilisateur (Admin)

![Détail utilisateur](/docs/screenshots/admin-user-detail.png)

_Statistiques et historique complet d'un utilisateur spécifique depuis le dashboard administrateur_

---

## Architecture du Projet

### Architecture de Sécurité

Le projet implémente une **architecture de sécurité en profondeur** avec plusieurs couches de protection.

**Principe clé** : Séparation des privilèges au niveau de la base de données

- 👤 `cash_user` : Droits limités (SELECT, INSERT, UPDATE) → Utilisé par défaut
- 👨‍💼 `cash_admin` : Tous les droits → Utilisé uniquement pour les opérations sensibles

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
├── Builders/                         # Builders (Patterns de construction)
│   └── CashRegisterBuilder.php        # Pattern Builder pour construire l'état
│
├── Entities/                         # Entités (Objets métier immutables)
│   ├── CashRegisterState.php          # État immutable de la caisse
│   └── Invoice.php                    # Entité facture immutable
│
├── Services/                         # Services (Pattern Decorator)
│   ├── InvoiceSender.php              # Interface pour l'envoi de factures
│   ├── BaseInvoiceSender.php          # Composant de base
│   ├── InvoiceSenderDecorator.php     # Décorateur abstrait
│   ├── EmailInvoiceSender.php         # Envoi par email
│   ├── PrintInvoiceSender.php         # Envoi par impression
│   ├── MailInvoiceSender.php          # Envoi par courrier postal
│   ├── SmsInvoiceSender.php           # Envoi par SMS
│   └── TemplateEngine.php             # Moteur de templates
│
├── Templates/                        # Templates de factures
│   ├── email.html                     # Template HTML pour email
│   ├── print.html                     # Template HTML pour impression
│   ├── mail.txt                       # Template texte pour courrier
│   ├── sms.txt                        # Template SMS avec log
│   └── README.md                      # Documentation des templates
│
├── Models/                            # Modèles (Accès base de données)
│   ├── User.php                       # Modèle utilisateur (authentification)
│   ├── CashRegister.php               # Modèle caisse (état, calculs)
│   ├── Transaction.php                # Modèle transaction (historique)
│   ├── Invoice.php                    # Modèle facture (CRUD)
│   └── Currency.php                   # Configuration des billets/pièces
│
├── Controllers/                       # Contrôleurs (Logique applicative)
│   ├── AuthController.php             # Authentification (login/logout)
│   ├── CashRegisterController.php     # Gestion de la caisse (transactions)
│   ├── InvoiceController.php          # Gestion des factures (view/send)
│   └── AdminController.php            # Administration (dashboard, stats)
│
└── Views/                             # Vues (Interface utilisateur)
    ├── login.php                      # Page de connexion
    ├── cash_register_form.php         # Formulaire de saisie caisse
    ├── cash_register_result.php       # Affichage des résultats + facture
    ├── history.php                    # Historique utilisateur + factures
    ├── invoice_view.php               # Visualisation de facture
    ├── admin/                         # Vues administrateur
    │   ├── dashboard.php              # Dashboard admin
    │   ├── history.php                # Historique global
    │   └── user_detail.php            # Détail utilisateur
    └── style.css                      # Styles CSS (1150+ lignes)

storage/                              # Fichiers générés
├── emails/                           # Factures email (HTML)
├── prints/                           # Factures impression (HTML)
├── mail/                             # Factures courrier (TXT)
└── sms/                              # Logs SMS (TXT)

database/
└── init.php                           # Script d'initialisation de la BDD (PHP)
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

### Pattern Builder

Le projet implémente le **Pattern Builder** pour construire l'état de la caisse de manière fluide et flexible.

### Pattern Decorator

Le projet implémente le **Pattern Decorator** pour le système de facturation, permettant d'ajouter dynamiquement des fonctionnalités d'envoi de factures.

**Classes impliquées :**

- `CashRegisterState` : Classe immutable représentant l'état de la caisse (billets + pièces)
- `CashRegisterBuilder` : Builder permettant de construire un `CashRegisterState` de manière fluide

**Avantages :**

- ✅ **Lisibilité** : Construction explicite et claire de l'état de la caisse
- ✅ **Flexibilité** : Plusieurs méthodes de création (par défaut, vide, personnalisée)
- ✅ **Validation** : Valeurs automatiquement validées (pas de valeurs négatives)
- ✅ **Immutabilité** : L'objet créé ne peut pas être modifié (garantit la cohérence)
- ✅ **Testabilité** : Facile à tester et à mocker dans les tests unitaires

**Documentation complète** : Voir [docs/builder-pattern-example.md](docs/builder-pattern-example.md)

**Classes impliquées :**

- `InvoiceSender` : Interface définissant le contrat d'envoi
- `BaseInvoiceSender` : Composant de base créant la facture en BDD
- `InvoiceSenderDecorator` : Décorateur abstrait pour enrichir les fonctionnalités
- `EmailInvoiceSender` : Décorateur pour envoi par email (HTML)
- `PrintInvoiceSender` : Décorateur pour impression (HTML)
- `MailInvoiceSender` : Décorateur pour courrier postal (TXT)
- `SmsInvoiceSender` : Décorateur pour envoi SMS (TXT)

**Avantages :**

- ✅ **Flexibilité** : Ajout dynamique de fonctionnalités d'envoi
- ✅ **Extensibilité** : Facile d'ajouter de nouveaux modes d'envoi
- ✅ **Combinable** : Possibilité d'envoyer par plusieurs canaux simultanément
- ✅ **Open/Closed Principle** : Extension sans modification du code existant
- ✅ **Single Responsibility** : Chaque décorateur a une responsabilité unique

**Documentation complète** : Voir [app/Templates/README.md](app/Templates/README.md) pour le système de templates.

## Fonctionnalités

### Système d'Authentification

- **Login sécurisé** : Page de connexion avec validation des identifiants
- **Gestion des sessions** : Sessions PHP sécurisées avec vérification automatique
- **Rôles utilisateurs** :
  - 👤 **Utilisateur** : Accès à sa caisse personnelle et son historique
  - 👨‍💼 **Administrateur** : Vue d'ensemble de tous les utilisateurs et leurs activités
- **Middleware** : Protection automatique des pages selon les droits d'accès
- **Déconnexion** : Bouton de déconnexion sur toutes les pages

### Calcul de Monnaie

- **Algorithme glouton (standard)** : Optimise le nombre de billets/pièces à rendre (du plus grand au plus petit)
- **Algorithme inversé** : Rendu de monnaie du plus petit au plus grand
- **Valeur préférée** : Option pour privilégier une dénomination spécifique (ex: maximiser les pièces de 1€)
- **Validation** : Vérifie la disponibilité en caisse
- **Précision** : Calculs en centimes pour éviter les erreurs de flottants

### Interface Utilisateur

- **Design moderne** : Interface responsive avec dégradés et animations
- **Images réelles** : Billets et pièces d'euros officiels de la BCE
- **Badges visuels** : Mise en évidence de la monnaie à rendre
- **Codes couleurs** :
  - 🔵 Bleu/Violet : Interface utilisateur standard
  - 🟠 Orange : Dashboard administrateur
  - 🟢 Vert : Entrées d'argent
  - 🔴 Rouge : Sorties d'argent
- **Responsive** : Compatible desktop, tablette et mobile

### Gestion de Caisse (Utilisateur)

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
  - Accès aux factures associées à chaque transaction

### Système de Facturation

- **Génération automatique** : Création d'une facture pour chaque transaction
- **Numéro unique** : Format `INV-YYYYMMDD-HHMMSS` (ex: `INV-20251121-143045`)
- **Association** : Lien entre facture et transaction via clé étrangère
- **Multi-formats** : Support de 4 formats d'envoi différents
  - 📧 **Email** : Format HTML riche avec styles inline
  - 🖨️ **Impression** : Format HTML optimisé pour impression
  - 📮 **Courrier** : Format texte ASCII pour envoi postal
  - 📱 **SMS** : Format texte court avec log détaillé
- **Système de templates** : Templates personnalisables avec variables `{{variable}}`
  - Templates HTML pour email/impression
  - Templates TXT pour courrier/SMS
  - Documentation complète dans `app/Templates/README.md`
- **Pattern Decorator** : Architecture extensible pour ajouter facilement de nouveaux canaux
- **Statuts de facture** :
  - `pending` : En attente
  - `sent_email` : Envoyée par email
  - `sent_print` : Préparée pour impression
  - `sent_mail` : Préparée pour courrier
  - `sent_sms` : Envoyée par SMS
- **Visualisation** : Consultation de la facture au format HTML dans le navigateur
- **Stockage** : Fichiers générés dans `storage/` (emails, prints, mail, sms)
- **Actions disponibles** :
  - 👁️ Voir la facture
  - 📧 Envoyer par email
  - 🖨️ Préparer pour impression
  - 📮 Préparer pour courrier
  - 📱 Envoyer par SMS
  - 📤 Envoyer par tous les moyens

### Dashboard Administrateur

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

## Installation et Utilisation

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
cp .env.exemple .env

# ⚠️ IMPORTANT : Éditer le fichier .env et modifier les mots de passe !
# En développement, vous pouvez garder les valeurs par défaut
# En production, TOUS les mots de passe doivent être modifiés

nano .env  # ou vim, code, etc.
```

**Mots de passe à modifier en production** :

- `DB_PASSWORD` : Mot de passe de l'utilisateur MySQL standard
- `DB_ADMIN_PASSWORD` : Mot de passe de l'utilisateur MySQL admin
- `DB_ROOT_PASSWORD` : Mot de passe root MySQL (pour l'initialisation)

Le fichier `.env` contient les paramètres de connexion à la base de données avec **deux utilisateurs MySQL distincts** pour une sécurité renforcée :

```env
# Configuration de base
DB_HOST=db                              # Nom du service Docker (ne pas modifier)
DB_PORT=3306                            # Port MySQL
DB_NAME=cash                            # Nom de la base de données

# Utilisateur STANDARD (droits limités : SELECT, INSERT, UPDATE)
DB_USER=cash_user                       # Utilisé pour les opérations courantes
DB_PASSWORD=user_password_secure_2024   # À modifier en production !

# Utilisateur ADMIN (tous les droits : incluant DELETE, DROP, ALTER)
DB_ADMIN_USER=cash_admin                # Utilisé pour les opérations d'administration
DB_ADMIN_PASSWORD=admin_password_secure_2024  # À modifier en production !

# Utilisateur ROOT (initialisation uniquement)
DB_ROOT_PASSWORD=rootpassword           # Utilisé uniquement au démarrage de Docker
```

### Principe de séparation des privilèges

L'application utilise **deux utilisateurs MySQL distincts** pour respecter le **principe du moindre privilège** :

| Utilisateur    | Droits                 | Usage                                    | Sécurité                               |
| -------------- | ---------------------- | ---------------------------------------- | -------------------------------------- |
| **cash_user**  | SELECT, INSERT, UPDATE | Opérations quotidiennes de l'application | ✅ Ne peut pas supprimer de données    |
| **cash_admin** | ALL PRIVILEGES         | Opérations d'administration sensibles    | ⚠️ À utiliser uniquement si nécessaire |

Cette séparation des droits **limite les dégâts** en cas de compromission de l'application :

- L'utilisateur standard ne peut pas supprimer de données (pas de DELETE)
- L'utilisateur standard ne peut pas modifier la structure de la base (pas de DROP, ALTER)
- L'utilisateur admin n'est utilisé que pour des opérations explicitement sensibles

⚠️ **Important** : Le fichier `.env` est ignoré par Git pour des raisons de sécurité. Ne jamais commit ce fichier avec des identifiants réels.

### Démarrage

3. **Lancer Docker Compose** :

```bash
docker compose up
```

4. **Attendre l'initialisation** :
   La première fois, Docker va :

   - Construire l'image PHP avec les extensions PDO MySQL
   - Installer Composer et les dépendances PHP (autoloader PSR-4)
   - Télécharger l'image MySQL
   - Initialiser automatiquement la base de données avec le script `database/init.php`
   - Cela peut prendre quelques minutes

5. **Accéder à l'application** :
   Ouvrir le navigateur à l'adresse : http://localhost:8000

   Vous serez redirigé vers la page de connexion.

6. **Arrêter le serveur** :

```bash
# Ctrl+C dans le terminal, puis :
docker compose down
```

### Réinitialisation de la base de données

#### Méthode 1 : Réinitialisation rapide (sans supprimer les volumes)

Pour réexécuter uniquement le script d'initialisation :

```bash
# Supprimer le flag d'initialisation et réexécuter init.php
docker compose exec php rm -f /tmp/.db_initialized && docker compose exec php php /var/www/database/init.php
```

#### Méthode 2 : Réinitialisation complète (recommandé)

Pour tout réinitialiser (conteneurs + volumes) :

```bash
# Arrêter les conteneurs et supprimer les volumes
docker compose down -v

# Relancer l'application (la base sera recréée automatiquement)
docker compose up
```

Après cette opération :

- ✅ Les utilisateurs MySQL (`cash_user` et `cash_admin`) seront créés avec les bons droits
- ✅ Les mots de passe des comptes applicatifs seront correctement hashés
- ✅ Les mots de passe MySQL proviendront du fichier `.env` (non commité)

## Technologies Utilisées

### Backend

- **PHP 8.4** : POO avancée avec namespaces PSR-4
- **MySQL 8.0** : Base de données relationnelle
- **PDO** : Couche d'abstraction avec requêtes préparées

### Architecture

- **MVC** : Pattern Model-View-Controller
- **PSR-4** : Autoloading automatique des classes
- **Singleton** : Pattern pour la connexion BDD
- **Builder** : Pattern créationnel pour construire l'état de la caisse
- **Decorator** : Pattern structurel pour le système de facturation
- **Entity** : Objets métier immutables (CashRegisterState, Invoice)
- **Front Controller** : Point d'entrée unique
- **Routing** : URLs propres et RESTful
- **Template Engine** : Système de templates avec variables `{{variable}}`

### Frontend

- **HTML5/CSS3** : Interface responsive
- **Design moderne** : Dégradés, animations, responsive

### Infrastructure

- **Docker** : Conteneurisation complète
- **Apache** : Serveur web avec mod_rewrite

## Sécurité

### Sécurité de l'application

✅ **Authentification** : Système de login avec sessions PHP sécurisées  
✅ **Gestion des rôles** : Middleware pour protéger les pages selon les droits d'accès  
✅ **Hashage des mots de passe** : Utilisation de `password_hash()` et `password_verify()`  
✅ **Injections SQL** : Protection via requêtes préparées PDO  
✅ **Injections XSS** : Échappement des données avec htmlspecialchars()  
✅ **Typage strict** : Validation et typage des données (intval(), floatval())  
✅ **Méthodes HTTP** : Vérification POST uniquement pour les formulaires  
✅ **Variables d'environnement** : Identifiants sensibles dans fichier .env  
✅ **Contrôle de version** : Fichier .env exclu de Git via .gitignore  
✅ **Gestion des erreurs** : Logging côté serveur (error_log)  
✅ **Sessions sécurisées** : Démarrage automatique et destruction propre

### Sécurité de la base de données

✅ **Séparation des privilèges** : Deux utilisateurs MySQL avec droits adaptés  
✅ **Principe du moindre privilège** : Utilisateur standard limité (SELECT, INSERT, UPDATE)  
✅ **Protection contre les suppressions** : L'utilisateur standard ne peut pas DELETE  
✅ **Protection structurelle** : L'utilisateur standard ne peut pas DROP/ALTER  
✅ **Connexions multiples** : `getInstance()` (user) et `getAdminInstance()` (admin)  
✅ **Isolation des rôles** : Réduction de la surface d'attaque en cas de compromission

### Sécurité des mots de passe applicatifs

Les mots de passe des utilisateurs de l'application sont **hashés de manière sécurisée** avec bcrypt :

- ✅ Tous les mots de passe sont hashés avec `PASSWORD_DEFAULT` (bcrypt)
- ✅ Vérification sécurisée avec `password_verify()`
- ✅ Les comptes de démonstration utilisent également des mots de passe hashés
- ✅ Les mots de passe ne sont jamais stockés en clair dans la base de données

### Sécurité des identifiants MySQL

Les identifiants de connexion MySQL sont gérés de manière sécurisée :

- ✅ Mots de passe stockés uniquement dans `.env` (ignoré par Git)
- ✅ Injection via variables d'environnement (pas de mots de passe en dur dans le code)
- ✅ Script d'initialisation PHP `init.php` qui utilise les variables d'environnement
- ✅ Pas de fichiers SQL avec des mots de passe en clair

**Documentation complète** : Voir `database/SECURITY.md` pour plus de détails sur la sécurité de la base de données.

## Configuration

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

**Initialisation sécurisée de la base** :

- Le script `database/init.php` utilise les variables d'environnement pour les mots de passe
- Les mots de passe ne sont **jamais stockés en dur** dans le code
- ✅ Gestion centralisée en PHP pour plus de flexibilité et de sécurité

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
- `invoices` : Factures associées aux transactions
  - Colonnes : id, transaction_id (FK), invoice_number (unique), user_id, status
  - Lien avec transaction_history via clé étrangère
  - Statuts : pending, sent_email, sent_print, sent_mail, sent_sms

**Utilisateurs de démonstration** :
| Email | Mot de passe | Rôle | Accès |
|-------|--------------|------|-------|
| `user1@cash.com` | `12345` | 👤 Utilisateur | Caisse + Historique personnel |
| `user2@cash.com` | `12345` | 👤 Utilisateur | Caisse + Historique personnel |
| `admin@cash.com` | `123456` | 👨‍💼 Admin | Dashboard + Vue d'ensemble |

**État initial de la caisse** :

- 1×500€, 2×200€, 2×100€, 4×50€, 1×20€, 23×10€, 0×5€
- 34×2€, 23×1€, 23×0.50€, 80×0.20€, 12×0.10€, 8×0.05€, 45×0.02€, 12×0.01€

## Projet Pédagogique

Ce projet fait partie du module "**Développement Sécurisé PHP**" à **LiveCampus - ESDID-26.2** et démontre :

### Compétences techniques

#### Architecture & Patterns

- ✅ **MVC** : Séparation Model-View-Controller
- ✅ **POO avancée** : Classes abstraites, héritage, namespaces PSR-4
- ✅ **Design Patterns** : Singleton, Builder, Decorator, Front Controller, Entity
- ✅ **SOLID** : Principes de conception orientée objet
- ✅ **Autoloading** : PSR-4 avec chargement automatique
- ✅ **Template Engine** : Système de templates personnalisé

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
- 🧾 Système de facturation complet avec envoi multi-formats
- 📧 Génération de factures HTML/TXT avec templates personnalisables
- 🎨 Pattern Decorator pour extensibilité des modes d'envoi
- 📊 Historique avec filtrage par utilisateur et factures associées
- 💾 Persistance des données en base avec relations
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
