# 📝 Fiche de Révision - Développement Sécurisé PHP

## 🏗️ Architecture Globale

### MVC (Model-View-Controller)

**C'est quoi ?** Une façon d'organiser ton code en 3 parties distinctes.

- **Model** : Gère les données (base de données)
- **View** : Affiche l'interface (HTML/CSS)
- **Controller** : Fait le lien entre les deux (logique)

**Pourquoi ?** Séparation des responsabilités = code plus propre et maintenable.

---

## 📁 Dossiers du Projet

### `Core/`

**Les fondations de l'app** - Classes de base que tout le monde utilise.

- **Database** : Connexion à MySQL (pattern Singleton)
- **Router** : Analyse l'URL et route vers le bon contrôleur
- **Controller** : Classe parent de tous les contrôleurs
- **Model** : Classe parent de tous les modèles
- **Session** : Gestion des sessions utilisateur

### `Models/`

**Accès aux données** - Classes qui parlent à la base de données.

- Exemples : User, CashRegister, Transaction, Invoice
- Ils font les requêtes SQL (SELECT, INSERT, UPDATE)
- Utilisent PDO avec requêtes préparées (sécurité)

### `Controllers/`

**Logique métier** - Le chef d'orchestre de l'application.

- Reçoit la requête utilisateur
- Appelle les Models pour récupérer/modifier des données
- Affiche les Views avec les données
- Exemples : AuthController (login), CashRegisterController (transactions)

### `Views/`

**Interface utilisateur** - Ce que l'utilisateur voit (HTML/CSS).

- Pas de logique ici, juste de l'affichage
- Utilise les données fournies par les Controllers
- Exemples : login.php, cash_register_form.php, history.php

### `Entities/`

**Objets métier immutables** - Représentent des concepts métier.

- `CashRegisterState` : L'état de la caisse (billets/pièces)
- `Invoice` : Une facture
- **Immutables** = Une fois créés, on ne peut plus les modifier (sécurité/fiabilité)

### `Builders/`

**Pattern Builder** - Construire des objets complexes étape par étape.

- `CashRegisterBuilder` : Construit un état de caisse de manière fluide
- Permet de créer des objets complexes sans constructeur géant
- Exemple : `$state = (new CashRegisterBuilder())->setEur500(5)->build();`

### `Interfaces/`

**Contrats** - Définissent ce qu'une classe DOIT faire.

- `InvoiceSenderInterface` : Toute classe qui envoie des factures doit avoir une méthode `send()`
- Utile pour le polymorphisme et les tests

### `Services/`

**Logique réutilisable** - Classes utilitaires qui font des trucs spécifiques.

- `EmailInvoiceSender`, `PrintInvoiceSender`, `SmsInvoiceSender` : Envoi de factures
- `TemplateEngine` : Remplace les `{{variables}}` dans les templates
- Utilisent le **Pattern Decorator** (voir ci-dessous)

### `Templates/`

**Modèles de factures** - Fichiers texte avec variables.

- `email.html`, `print.html`, `mail.txt`, `sms.txt`
- Contiennent des placeholders `{{invoice_number}}` qui sont remplacés dynamiquement

---

## 🎨 Patterns de Conception

### Pattern Singleton (Core/Database)

**Principe** : Une seule instance de la classe pour toute l'application.

```php
Database::getInstance(); // Toujours la même connexion
```

**Pourquoi ?** Évite de créer 50 connexions MySQL. Une seule suffit.

### Pattern Builder (Builders/CashRegisterBuilder)

**Principe** : Construire des objets complexes étape par étape.

```php
$state = (new CashRegisterBuilder())
    ->setEur500(2)
    ->setEur200(5)
    ->build();
```

**Pourquoi ?** Plus lisible qu'un constructeur avec 20 paramètres.

### Pattern Decorator (Services/)

**Principe** : Ajouter des fonctionnalités dynamiquement à un objet.

```php
$sender = new BaseInvoiceSender();
$sender = new EmailInvoiceSender($sender); // Ajoute envoi email
$sender = new PrintInvoiceSender($sender); // Ajoute impression
$sender->send($invoice); // Envoie par email ET impression
```

**Pourquoi ?** Combiner plusieurs fonctionnalités sans modifier le code existant.

### Pattern MVC (toute l'app)

**Principe** : Séparer les données, la logique et l'affichage.

```
Utilisateur → Router → Controller → Model → Database
                            ↓
                          View → HTML
```

**Pourquoi ?** Chaque partie a un rôle précis = code organisé.

---

## 🔧 Concepts Techniques

### Autoloader PSR-4

**C'est quoi ?** Chargement automatique des classes.

```php
use App\Models\User; // PHP charge automatiquement app/Models/User.php
```

**Pourquoi ?** Plus besoin de `require_once` partout. Composer fait le travail.

### Namespaces

**C'est quoi ?** Organiser les classes par dossiers.

```php
namespace App\Controllers; // Je suis dans app/Controllers/
```

**Pourquoi ?** Évite les conflits de noms (deux classes peuvent s'appeler "User").

### Requêtes Préparées (PDO)

**C'est quoi ?** Séparer le SQL des données pour éviter les injections SQL.

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

**Pourquoi ?** Sécurité ! Empêche les hackers d'injecter du SQL malveillant.

### Sessions PHP

**C'est quoi ?** Garder des informations sur l'utilisateur entre les pages.

```php
$_SESSION['user_id'] = 1; // L'utilisateur reste connecté
```

**Pourquoi ?** Savoir qui est connecté sans redemander le mot de passe à chaque page.

### Templates & Variables

**C'est quoi ?** Fichiers avec des placeholders remplacés dynamiquement.

```html
<h1>Facture {{invoice_number}}</h1>
```

devient

```html
<h1>Facture INV-20251121-143045</h1>
```

**Pourquoi ?** Séparer le contenu de la structure. Facile à modifier.

---

## 🔐 Sécurité

### Hashage des mots de passe

**Ne JAMAIS stocker en clair !**

```php
$hash = password_hash($password, PASSWORD_DEFAULT); // Stocke ça
$valid = password_verify($password, $hash); // Vérifie ça
```

### Protection XSS

**Échapper les données affichées**

```php
echo htmlspecialchars($user_input); // Évite l'injection de code HTML/JS
```

### Séparation des privilèges MySQL

**Deux utilisateurs** : un avec droits limités (SELECT, INSERT, UPDATE), un admin (tout).
**Pourquoi ?** Si l'app est hackée, impossible de supprimer des données avec l'utilisateur standard.

---

## 🚀 Flux de l'Application

1. **Requête HTTP** arrive sur `index.php` (Front Controller)
2. **bootstrap.php** initialise l'app (session, autoloader)
3. **Router** analyse l'URL et trouve la route
4. **Controller** reçoit la requête
5. **Model** récupère/modifie les données en base
6. **View** affiche le HTML avec les données
7. **Réponse HTTP** renvoyée au navigateur

---

## 💡 En Résumé

| Concept        | En une phrase                                                     |
| -------------- | ----------------------------------------------------------------- |
| **MVC**        | Séparer données (Model), affichage (View) et logique (Controller) |
| **Autoloader** | Charger automatiquement les classes (plus de `require`)           |
| **Builder**    | Construire des objets complexes étape par étape                   |
| **Decorator**  | Ajouter des fonctionnalités sans modifier le code existant        |
| **Singleton**  | Une seule instance (ex: connexion base de données)                |
| **Namespaces** | Organiser les classes par dossiers                                |
| **PDO**        | Accès base de données sécurisé (requêtes préparées)               |
| **Templates**  | Fichiers avec variables `{{var}}` remplacées dynamiquement        |
| **Entities**   | Objets métier immutables (pas modifiables)                        |
| **Interfaces** | Contrats que les classes doivent respecter                        |

---

## 🎯 Points Clés à Retenir

✅ **MVC** = Organisation claire du code  
✅ **Autoloader PSR-4** = Plus besoin de `require`  
✅ **Design Patterns** = Solutions éprouvées à des problèmes récurrents  
✅ **Sécurité** = Requêtes préparées + hash passwords + échapper HTML  
✅ **Séparation** = Chaque classe a UN rôle précis  
✅ **Immutabilité** = Objets qu'on ne peut pas modifier après création
