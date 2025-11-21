#!/bin/bash
set -e

# Installer les dépendances Composer si l'autoloader n'existe pas
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "📦 Installation des dépendances Composer..."
    cd /var/www
    composer install --no-dev --optimize-autoloader --no-interaction
    echo "✅ Dépendances installées avec succès"
else
    echo "✅ Dépendances Composer déjà installées"
fi

# Exécuter la commande passée au conteneur
exec "$@"

