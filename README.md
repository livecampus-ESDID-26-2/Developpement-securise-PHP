# Développement Sécurisé PHP

## Prérequis

Ce projet nécessite PHP installé sur votre machine.

### Installation de PHP

#### Sur macOS

Télécharger et installer Homebrew :

```bash
curl -o- https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh | bash
```

Installer et lier PHP 8.4 :

```bash
brew install php@8.4
brew link --force --overwrite php@8.4
```

#### Sur Windows

Téléchargez PHP depuis [php.net](https://www.php.net/downloads.php) et suivez les instructions d'installation.

## Lancement du serveur de développement

Pour démarrer le serveur PHP intégré, exécutez la commande suivante à la racine du projet :

```bash
php -S localhost:8000
```

Le serveur sera accessible à l'adresse : [http://localhost:8000](http://localhost:8000)

Pour arrêter le serveur, appuyez sur `Ctrl+C` dans le terminal.
