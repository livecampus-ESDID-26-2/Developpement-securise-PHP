# 💰 Système de Caisse Enregistreuse - Développement Sécurisé PHP

**École :** LiveCampus - ESDID-26.2  
**Étudiant :** MASIA Antoine

## 📋 Description du Projet

Application PHP de gestion de caisse enregistreuse permettant de :
- Calculer automatiquement la monnaie à rendre
- Optimiser le rendu de monnaie (algorithme glouton)
- Gérer l'état de la caisse (entrées/sorties)
- Afficher visuellement les billets et pièces

## 🏗️ Architecture du Projet

```
app/
├── index.php                          # Point d'entrée principal
│
├── config/                            # Configuration
│   ├── config.php                     # Chemins et constantes globales
│   ├── database.php                   # Connexion et fonctions base de données
│   └── monnaie.php                    # Configuration billets/pièces avec images
│
├── backend/                           # Logique métier (PHP)
│   ├── systeme_caisse.php            # Page principale de la caisse
│   └── traitement_caisse.php         # Calculs et traitement des transactions
│
└── views/                             # Interface utilisateur (HTML/CSS)
    ├── formulaire_caisse.php         # Formulaire de saisie
    ├── resultat_caisse.php           # Affichage des résultats
    └── style.css                      # Styles CSS

database/
└── init.sql                           # Script d'initialisation de la base de données
```

## ✨ Fonctionnalités

### 💵 Calcul de Monnaie
- **Algorithme glouton** : Optimise le nombre de billets/pièces à rendre
- **Valeur préférée** : Option pour privilégier une dénomination spécifique (ex: maximiser les pièces de 1€)
- **Validation** : Vérifie la disponibilité en caisse
- **Précision** : Calculs en centimes pour éviter les erreurs de flottants

### 🎨 Interface Utilisateur
- **Design moderne** : Interface responsive avec dégradés
- **Images réelles** : Billets et pièces d'euros officiels
- **Badges visuels** : Mise en évidence de la monnaie à rendre
- **Codes couleurs** :
  - 🔵 Bleu/Violet : Monnaie rendue (standard)
  - 🟠 Orange : Valeur préférée (avec animation)
  - 🟢 Vert : Entrées d'argent
  - 🔴 Rouge : Sorties d'argent

### 📊 Gestion de Caisse
- **État initial** : Affichage de la caisse avant transaction
- **Nouvel état** : Affichage après transaction avec différences
- **Comparaison** : Vue avant/après côte à côte
- **Persistance** : Sauvegarde de l'état de la caisse en base de données
- **Historique** : Enregistrement de toutes les transactions

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

6. **Arrêter le serveur** :
```bash
# Ctrl+C dans le terminal, puis :
docker compose down
```

## 🔧 Technologies Utilisées

- **PHP 8.4** : Backend avec extensions PDO MySQL
- **MySQL 8.0** : Base de données
- **HTML5/CSS3** : Frontend
- **Docker** : Conteneurisation
- **Architecture MVC** : Séparation des responsabilités

## 🔒 Sécurité

✅ Validation des entrées côté serveur  
✅ Protection contre les injections SQL (requêtes préparées PDO)  
✅ Protection contre les injections XSS (htmlspecialchars())  
✅ Typage strict des données (intval(), floatval())  
✅ Vérification de la méthode HTTP (POST uniquement)  
✅ Variables d'environnement pour les identifiants sensibles  
✅ Fichier `.env` exclu du contrôle de version  
✅ Gestion des erreurs avec logging

## 📝 Configuration

### Constantes (config/config.php)
- `ROOT_PATH` : Chemin vers le dossier `app/`
- `BACKEND_PATH` : Chemin vers `backend/`
- `VIEWS_PATH` : Chemin vers `views/`
- `CONFIG_PATH` : Chemin vers `config/`

### Base de Données

**Tables créées automatiquement** :
- `users` : Utilisateurs du système (avec rôles user/admin)
- `caisse_state` : État actuel de la caisse (dernier enregistrement = état actuel)
- `caisse_history` : Historique complet des transactions

**Utilisateurs par défaut** :
- `user1@cash.com` / `12345` (utilisateur)
- `user2@cash.com` / `12345` (utilisateur)
- `admin@cash.com` / `123456` (administrateur)

## 🎓 Projet Pédagogique

Ce projet fait partie du module "Développement Sécurisé PHP" et démontre :
- Architecture modulaire
- Séparation des responsabilités
- Bonnes pratiques de sécurité PHP
- Algorithmes d'optimisation
- Interface utilisateur moderne
