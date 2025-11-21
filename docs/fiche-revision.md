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

## 📦 Git - Les Bases du Versioning

### Initialisation et Configuration

**git init** : Initialise un nouveau dépôt Git dans le dossier courant.

```bash
git init
```

**C'est quoi ?** Crée un dossier `.git/` caché qui contient tout l'historique du projet.

**Pourquoi ?** Pour commencer à versionner ton code et garder un historique de tous les changements.

### Suivi des Fichiers

**git status** : Affiche l'état des fichiers dans le dépôt.

```bash
git status
```

**Affiche** :

- Les fichiers modifiés
- Les fichiers ajoutés à l'index (staging)
- Les fichiers non suivis (untracked)
- La branche courante

**git add** : Ajoute des fichiers à l'index (staging area) avant un commit.

```bash
git add fichier.php          # Un fichier spécifique
git add .                     # Tous les fichiers modifiés
git add app/Models/*.php      # Avec pattern
```

**Pourquoi ?** Permet de choisir quels changements seront dans le prochain commit.

### Commits

**git commit -m 'message'** : Crée un nouveau commit avec un message.

```bash
git commit -m "Ajout du système d'authentification"
```

**C'est quoi ?** Un snapshot (photo) de ton code à un instant T.

**Bonnes pratiques** :

- Messages clairs et descriptifs
- Un commit = une fonctionnalité/correction
- Utiliser l'impératif ("Ajoute" plutôt que "Ajouté")

### Branches

**git branch nom_branche** : Crée une nouvelle branche.

```bash
git branch feature/login
```

**C'est quoi ?** Une ligne de développement parallèle.

**Pourquoi ?** Développer des fonctionnalités sans toucher à la branche principale (main/master).

**git checkout nom_branche** : Bascule sur une autre branche.

```bash
git checkout feature/login
git checkout -b feature/register  # Crée ET bascule en une commande
```

### Fusion et Intégration

**git merge** : Fusionne une autre branche dans la branche courante.

```bash
git checkout main
git merge feature/login  # Fusionne feature/login dans main
```

**C'est quoi ?** Intègre les changements d'une branche dans une autre.

**Attention** : Peut créer des conflits si les mêmes lignes ont été modifiées.

### Travail avec un Dépôt Distant

**git fetch** : Récupère les changements d'un dépôt distant SANS les fusionner.

```bash
git fetch origin
```

**Différence avec pull** : `fetch` télécharge juste, `pull` = `fetch` + `merge`.

**git push** : Envoie les commits locaux vers le dépôt distant.

```bash
git push origin main
```

**C'est quoi ?** Synchronise ton code local avec GitHub/GitLab.

**git clone** : Copie un dépôt distant en local.

```bash
git clone https://github.com/user/repo.git
```

---

## 🐘 PHP - Les Fondamentaux

### Syntaxe de Base

**Balise d'ouverture PHP** : `<?php`

```php
<?php
// Ton code PHP ici
?>
```

**Autres balises** (déconseillées) :

- `<?` (short tags, nécessite configuration)
- `<?=` (echo court, ok pour affichage)

**Commentaires** :

```php
// Commentaire sur une ligne
# Aussi un commentaire (moins courant)
/* Commentaire
   sur plusieurs
   lignes */
```

### Affichage et Variables

**echo** : Affiche du texte ou des variables.

```php
echo "Hello World";
echo $variable;
echo "Bonjour " . $nom;  // Concaténation
```

**Alternatives** : `print`, `var_dump()`, `print_r()`

**Superglobales** : Variables automatiquement disponibles partout.

| Variable    | Contenu                              |
| ----------- | ------------------------------------ |
| `$_SERVER`  | Informations serveur et requête HTTP |
| `$_GET`     | Données de l'URL (?param=valeur)     |
| `$_POST`    | Données d'un formulaire en POST      |
| `$_SESSION` | Données de session (persistantes)    |
| `$_COOKIE`  | Cookies du navigateur                |
| `$_FILES`   | Fichiers uploadés                    |
| `$_ENV`     | Variables d'environnement            |

### Types et Fonctions

**gettype($var)** : Retourne le type d'une variable.

```php
$x = 42;
echo gettype($x);  // "integer"
```

**Types PHP** : `integer`, `double`, `string`, `boolean`, `array`, `object`, `NULL`, `resource`

**isset($var)** : Vérifie si une variable est définie et non null.

```php
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}
```

**Retourne** : `true` si définie et non null, `false` sinon.

**Différence** : `empty()` teste aussi si la valeur est "vide" (0, "", false, [])

### Comparaisons

**== vs ===** : Égalité vs égalité stricte.

```php
5 == "5"   // true  (compare les valeurs, conversion automatique)
5 === "5"  // false (compare valeurs ET types)

0 == false   // true
0 === false  // false (int vs bool)
```

**Bonnes pratiques** : Toujours utiliser `===` pour éviter les surprises.

### Inclusion de Fichiers

| Fonction       | Erreur si absent | Inclusion unique |
| -------------- | ---------------- | ---------------- |
| `include`      | Warning          | ❌               |
| `include_once` | Warning          | ✅               |
| `require`      | Erreur fatale    | ❌               |
| `require_once` | Erreur fatale    | ✅               |

```php
require_once 'config.php';   // Critique (config)
include 'header.php';         // Non critique (affichage)
```

### Formulaires et Redirections

**$\_POST** : Récupère les données d'un formulaire en POST.

```php
<form method="POST">
    <input type="email" name="email">
    <button type="submit">Envoyer</button>
</form>

<?php
$email = $_POST['email'] ?? '';  // ?? = valeur par défaut si non défini
?>
```

**header('Location: ...')** : Redirige vers une autre page.

```php
header('Location: dashboard.php');
exit; // Important ! Arrête l'exécution
```

**Pourquoi exit ?** Empêche l'exécution du code suivant.

---

## 🎯 PHP Orienté Objet

### Classes et Objets

**Définir une classe** :

```php
class User {
    public $name;
    public $email;

    public function greet() {
        return "Bonjour " . $this->name;
    }
}
```

**Créer un objet** :

```php
$user = new User();
$user->name = "Alice";
echo $user->greet();  // "Bonjour Alice"
```

### Visibilité (Encapsulation)

| Mot-clé     | Accessible depuis                        |
| ----------- | ---------------------------------------- |
| `public`    | Partout (classe, enfants, extérieur)     |
| `private`   | Uniquement dans la classe                |
| `protected` | Dans la classe et ses enfants (héritage) |

```php
class BankAccount {
    private $balance = 0;  // Caché de l'extérieur

    public function deposit($amount) {
        $this->balance += $amount;  // Méthode publique pour modifier
    }

    public function getBalance() {
        return $this->balance;
    }
}
```

**Encapsulation** : Cacher les détails internes d'un objet et fournir des méthodes publiques pour interagir.

**Pourquoi ?**

- Protège les données (impossible de mettre un solde négatif directement)
- Facilite les modifications internes sans casser le code externe

### Héritage

**extends** : Hérite d'une classe parent.

```php
class Animal {
    protected $name;

    public function eat() {
        echo $this->name . " mange";
    }
}

class Dog extends Animal {
    public function bark() {
        echo "Woof!";
    }
}

$dog = new Dog();
$dog->eat();   // Méthode héritée
$dog->bark();  // Méthode propre
```

### Constructeur

**\_\_construct()** : Méthode appelée automatiquement à l'instanciation.

```php
class User {
    private $name;
    private $email;

    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }
}

$user = new User("Alice", "alice@example.com");
```

### Méthodes et Propriétés Statiques

**static** : Lié à la classe, pas à une instance.

```php
class Math {
    public static $pi = 3.14;

    public static function add($a, $b) {
        return $a + $b;
    }
}

echo Math::$pi;          // 3.14
echo Math::add(5, 3);    // 8
```

**Appel** : `Classe::methode()` ou `Classe::$propriete`

**self:: vs $this->** :

```php
class Counter {
    private static $count = 0;  // Partagé entre toutes les instances
    private $id;                 // Propre à chaque instance

    public function __construct() {
        self::$count++;          // Accès à la propriété statique
        $this->id = self::$count; // Accès à la propriété d'instance
    }

    public static function getCount() {
        return self::$count;      // Dans une méthode statique, pas de $this
    }
}
```

- `$this->` : Accès aux propriétés/méthodes d'instance
- `self::` : Accès aux propriétés/méthodes statiques

### Constantes

**const** : Constante de classe.

```php
class Database {
    const HOST = 'localhost';
    const PORT = 3306;
}

echo Database::HOST;  // 'localhost'
```

**define()** : Constante globale.

```php
define('APP_NAME', 'Mon Application');
echo APP_NAME;  // 'Mon Application'
```

**Différence** : `const` dans une classe, `define()` globale (partout dans le code).

### Interfaces

**interface** : Définit un contrat que les classes doivent respecter.

```php
interface PaymentInterface {
    public function pay($amount);
    public function refund($amount);
}

class CreditCard implements PaymentInterface {
    public function pay($amount) {
        // Implémentation obligatoire
    }

    public function refund($amount) {
        // Implémentation obligatoire
    }
}
```

**Pourquoi ?**

- Force une structure commune
- Permet le polymorphisme
- Facilite les tests (mock)

**Iterator** : Interface pour rendre un objet itérable.

```php
class MyCollection implements Iterator {
    public function current() { }
    public function next() { }
    public function key() { }
    public function valid() { }
    public function rewind() { }
}

// Permet d'utiliser foreach
foreach ($collection as $item) {
    // ...
}
```

---

## 🎨 Design Patterns - Catalogue Complet

### Pattern Singleton

**But** : Garantir une seule instance d'une classe dans toute l'application.

```php
class Database {
    private static $instance = null;

    private function __construct() {
        // Connexion à la BDD
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

$db1 = Database::getInstance();
$db2 = Database::getInstance();  // Même instance que $db1
```

**Cas d'usage** :

- Connexion base de données
- Logger
- Configuration globale

**Avantages** :

- Une seule connexion = économie de ressources
- Point d'accès global

**Inconvénients** :

- Difficile à tester
- Peut créer des couplages

### Pattern Factory (Fabrique)

**But** : Centraliser la création d'objets.

```php
class VehicleFactory {
    public static function create($type) {
        switch ($type) {
            case 'car':
                return new Car();
            case 'bike':
                return new Bike();
            case 'truck':
                return new Truck();
            default:
                throw new Exception("Type inconnu");
        }
    }
}

$vehicle = VehicleFactory::create('car');
```

**Cas d'usage** :

- Création d'objets complexes
- Logique de création centralisée
- Objets avec beaucoup de dépendances

**Avantages** :

- Code de création au même endroit
- Facile à modifier
- Respecte le principe de responsabilité unique

### Pattern Builder (Constructeur)

**But** : Construire des objets complexes étape par étape.

```php
class CashRegisterBuilder {
    private $eur500 = 0;
    private $eur200 = 0;
    private $eur100 = 0;

    public function setEur500($count) {
        $this->eur500 = $count;
        return $this;  // Pour chaîner les appels
    }

    public function setEur200($count) {
        $this->eur200 = $count;
        return $this;
    }

    public function build() {
        return new CashRegisterState(
            $this->eur500,
            $this->eur200,
            $this->eur100
        );
    }
}

// Utilisation fluide
$state = (new CashRegisterBuilder())
    ->setEur500(5)
    ->setEur200(10)
    ->build();
```

**Cas d'usage** :

- Objets avec beaucoup de paramètres optionnels
- Construction en plusieurs étapes
- Différentes représentations du même objet

**Avantages** :

- Lisible et maintenable
- Évite les constructeurs avec 10+ paramètres
- Interface fluide (chaînage)

### Pattern Decorator (Décorateur)

**But** : Ajouter des fonctionnalités à un objet dynamiquement.

```php
interface InvoiceSenderInterface {
    public function send($invoice);
}

class BaseInvoiceSender implements InvoiceSenderInterface {
    public function send($invoice) {
        // Logique de base
    }
}

class EmailInvoiceSender implements InvoiceSenderInterface {
    private $wrapped;

    public function __construct(InvoiceSenderInterface $wrapped) {
        $this->wrapped = $wrapped;
    }

    public function send($invoice) {
        $this->wrapped->send($invoice);  // Appel du précédent
        // + Envoi par email
    }
}

// Empiler les décorateurs
$sender = new BaseInvoiceSender();
$sender = new EmailInvoiceSender($sender);
$sender = new PrintInvoiceSender($sender);
$sender->send($invoice);  // Base + Email + Print
```

**Cas d'usage** :

- Combiner plusieurs fonctionnalités
- Éviter l'explosion de sous-classes
- Fonctionnalités optionnelles

**Avantages** :

- Flexible (composition vs héritage)
- Respecte le principe ouvert/fermé
- Combine facilement des comportements

### Pattern Observer (Observateur)

**But** : Notifier plusieurs objets lorsqu'un état change.

```php
interface Observer {
    public function update($data);
}

class Subject {
    private $observers = [];

    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    public function notify($data) {
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }
}

class EmailNotifier implements Observer {
    public function update($data) {
        // Envoie un email
    }
}

class LogNotifier implements Observer {
    public function update($data) {
        // Log dans un fichier
    }
}

$subject = new Subject();
$subject->attach(new EmailNotifier());
$subject->attach(new LogNotifier());
$subject->notify("Nouveau message");  // Tous les observers sont notifiés
```

**Cas d'usage** :

- Système d'événements
- Notifications multiples
- Découplage entre composants

**Avantages** :

- Faible couplage
- Ajout facile de nouveaux observers
- Communication one-to-many

### Pattern Adapter (Adaptateur)

**But** : Rendre compatible deux interfaces différentes.

```php
// Interface attendue
interface PaymentGateway {
    public function processPayment($amount);
}

// Classe externe incompatible
class StripeAPI {
    public function charge($cents) {
        // API Stripe
    }
}

// Adaptateur
class StripeAdapter implements PaymentGateway {
    private $stripe;

    public function __construct(StripeAPI $stripe) {
        $this->stripe = $stripe;
    }

    public function processPayment($amount) {
        // Conversion euros → cents
        $cents = $amount * 100;
        return $this->stripe->charge($cents);
    }
}

// Utilisation
$gateway = new StripeAdapter(new StripeAPI());
$gateway->processPayment(50);  // Interface unifiée
```

**Cas d'usage** :

- Intégration de bibliothèques externes
- Migration progressive
- Uniformisation d'APIs différentes

**Avantages** :

- Réutilise du code existant
- Isole les dépendances externes
- Interface cohérente

### Pattern State (État)

**But** : Modifier le comportement d'un objet selon son état interne.

```php
interface OrderState {
    public function process($order);
    public function cancel($order);
}

class PendingState implements OrderState {
    public function process($order) {
        echo "Commande en cours de traitement";
        $order->setState(new ProcessingState());
    }

    public function cancel($order) {
        echo "Commande annulée";
        $order->setState(new CancelledState());
    }
}

class ProcessingState implements OrderState {
    public function process($order) {
        echo "Déjà en cours";
    }

    public function cancel($order) {
        echo "Impossible d'annuler";
    }
}

class Order {
    private $state;

    public function __construct() {
        $this->state = new PendingState();
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function process() {
        $this->state->process($this);
    }

    public function cancel() {
        $this->state->cancel($this);
    }
}
```

**Cas d'usage** :

- Machine à états (commandes, connexions, etc.)
- Comportements différents selon le contexte
- Éviter les gros `if/else` ou `switch`

**Avantages** :

- Code organisé par état
- Facile d'ajouter de nouveaux états
- Évite les conditions complexes

---

## 💡 En Résumé

| Concept             | En une phrase                                                    |
| ------------------- | ---------------------------------------------------------------- |
| **git init**        | Initialise un nouveau dépôt Git                                  |
| **git status**      | Affiche l'état des fichiers (modifiés, staging, etc.)            |
| **git add**         | Ajoute des fichiers à l'index avant commit                       |
| **git commit**      | Crée un snapshot du code avec un message                         |
| **git branch**      | Crée une nouvelle branche (ligne de développement)               |
| **git merge**       | Fusionne une branche dans la branche courante                    |
| **git fetch**       | Récupère les changements distants sans fusionner                 |
| **git push**        | Envoie les commits locaux vers le dépôt distant                  |
| **<?php**           | Balise d'ouverture PHP                                           |
| **echo**            | Affiche du texte ou des variables                                |
| **$\_SERVER**       | Superglobale avec infos serveur et requête                       |
| **$\_POST**         | Superglobale avec données de formulaire POST                     |
| **gettype()**       | Retourne le type d'une variable                                  |
| **isset()**         | Vérifie si une variable est définie et non null                  |
| **== vs ===**       | Égalité simple vs égalité stricte (avec type)                    |
| **include**         | Inclut un fichier (warning si absent)                            |
| **require**         | Inclut un fichier (erreur fatale si absent)                      |
| **header()**        | Redirige ou modifie les en-têtes HTTP                            |
| **class**           | Définit une classe (modèle d'objet)                              |
| **new**             | Crée une instance d'une classe                                   |
| **public**          | Accessible partout                                               |
| **private**         | Accessible uniquement dans la classe                             |
| **protected**       | Accessible dans la classe et ses enfants                         |
| **extends**         | Hérite d'une classe parent                                       |
| **\_\_construct()** | Constructeur appelé à l'instanciation                            |
| **static**          | Propriété/méthode liée à la classe, pas à l'instance             |
| **Classe::**        | Appel d'une méthode/propriété statique                           |
| **self::**          | Référence à la classe courante (statique)                        |
| **$this->**         | Référence à l'instance courante                                  |
| **const**           | Constante de classe                                              |
| **interface**       | Contrat que les classes doivent respecter                        |
| **Iterator**        | Interface pour rendre un objet itérable                          |
| **Singleton**       | Une seule instance (ex: connexion BDD)                           |
| **Factory**         | Centralise la création d'objets                                  |
| **Builder**         | Construit des objets complexes étape par étape                   |
| **Decorator**       | Ajoute des fonctionnalités dynamiquement                         |
| **Observer**        | Notifie plusieurs objets lors d'un changement                    |
| **Adapter**         | Rend compatible deux interfaces différentes                      |
| **State**           | Modifie le comportement selon l'état interne                     |
| **Encapsulation**   | Cache les détails internes d'un objet                            |
| **MVC**             | Sépare données (Model), affichage (View) et logique (Controller) |
| **Autoloader**      | Charge automatiquement les classes (plus de `require`)           |
| **Namespaces**      | Organise les classes par dossiers                                |
| **PDO**             | Accès BDD sécurisé (requêtes préparées)                          |
| **Templates**       | Fichiers avec variables `{{var}}` remplacées dynamiquement       |
| **Entities**        | Objets métier immutables (pas modifiables)                       |

---

## 🎯 Points Clés à Retenir

### Git

✅ **git init** = Initialise un dépôt Git  
✅ **git add + commit** = Ajoute et sauvegarde les changements  
✅ **Branches** = Développement parallèle (merge pour fusionner)  
✅ **fetch vs pull** = fetch télécharge, pull télécharge + fusionne

### PHP de Base

✅ **<?php** = Balise d'ouverture obligatoire  
✅ **echo** = Affichage de texte/variables  
✅ **Superglobales** = $\_POST, $\_GET, $\_SERVER, $\_SESSION  
✅ **=== vs ==** = Toujours utiliser === (compare type + valeur)  
✅ **Requêtes préparées** = Protection contre injection SQL

### PHP Orienté Objet

✅ **Encapsulation** = private/protected/public pour protéger les données  
✅ **Héritage** = extends pour réutiliser du code  
✅ **Interfaces** = Contrats que les classes doivent respecter  
✅ **static** = Lié à la classe (self::), pas à l'instance ($this->)  
✅ **\_\_construct()** = Initialisation automatique des objets

### Design Patterns

✅ **Singleton** = Une seule instance (connexion BDD)  
✅ **Factory** = Centralise la création d'objets  
✅ **Builder** = Construction fluide d'objets complexes  
✅ **Decorator** = Ajoute des fonctionnalités dynamiquement  
✅ **Observer** = Notifications multiples lors de changements  
✅ **Adapter** = Rend compatibles deux interfaces  
✅ **State** = Comportement selon l'état interne

### Architecture & Sécurité

✅ **MVC** = Séparation Model/View/Controller  
✅ **Autoloader PSR-4** = Chargement automatique des classes  
✅ **Namespaces** = Organisation par dossiers  
✅ **Sécurité** = Hash passwords + échapper HTML + requêtes préparées  
✅ **Immutabilité** = Objets non modifiables après création  
✅ **Séparation des responsabilités** = Une classe = un rôle
