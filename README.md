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
```

## ✨ Fonctionnalités

### 💵 Calcul de Monnaie

- **Algorithme glouton** : Optimise le nombre de billets/pièces à rendre
- **Validation** : Vérifie la disponibilité en caisse
- **Précision** : Calculs en centimes pour éviter les erreurs de flottants

### 🎨 Interface Utilisateur

- **Design moderne** : Interface responsive avec dégradés
- **Images réelles** : Billets et pièces d'euros officiels
- **Badges visuels** : Mise en évidence de la monnaie à rendre
- **Codes couleurs** :
  - 🟢 Vert : Entrées d'argent
  - 🔴 Rouge : Sorties d'argent

### 📊 Gestion de Caisse

- **État initial** : Affichage de la caisse avant transaction
- **Nouvel état** : Affichage après transaction avec différences
- **Comparaison** : Vue avant/après côte à côte

## 🚀 Installation et Utilisation

### Prérequis

- Docker
- Docker Compose

### Démarrage

1. **Cloner le projet** :

```bash
git clone [https://github.com/livecampus-ESDID-26-2/Developpement-securise-PHP](https://github.com/livecampus-ESDID-26-2/Developpement-securise-PHP)
cd Developpement-securise-PHP
```

2. **Lancer Docker Compose** :

```bash
docker compose up
```

3. **Accéder à l'application** :
   Ouvrir le navigateur à l'adresse : [http://localhost:8000](http://localhost:8000)

4. **Arrêter le serveur** :

```bash
# Ctrl+C dans le terminal, puis :
docker compose down
```

## 🔧 Technologies Utilisées

- **PHP 8.4** : Backend
- **HTML5/CSS3** : Frontend
- **Docker** : Conteneurisation
- **Architecture MVC** : Séparation des responsabilités

## 🔒 Sécurité

- ✅ Validation des entrées côté serveur
- ✅ Protection contre les injections (`htmlspecialchars()`)
- ✅ Typage strict des données (`intval()`, `floatval()`)
- ✅ Vérification de la méthode HTTP (POST uniquement)
- ✅ Gestion des erreurs

## 📝 Structure des Constantes

Le fichier `config/config.php` définit :

- `ROOT_PATH` : Chemin vers le dossier app/
- `BACKEND_PATH` : Chemin vers backend/
- `VIEWS_PATH` : Chemin vers views/
- `CONFIG_PATH` : Chemin vers config/

## 🎓 Projet Pédagogique

Ce projet fait partie du module "Développement Sécurisé PHP" et démontre :

- Architecture modulaire
- Séparation des responsabilités
- Bonnes pratiques de sécurité PHP
- Algorithmes d'optimisation
- Interface utilisateur moderne
