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

# Créer et donner les permissions au dossier storage
if [ -d "/var/www/storage" ]; then
    echo "📁 Configuration des permissions du dossier storage..."
    chmod -R 777 /var/www/storage
    echo "✅ Permissions configurées"
fi

# Attendre que MySQL soit prêt et initialiser la base de données
echo "⏳ Attente de MySQL..."
max_attempts=30
attempt=0
# Utiliser DB_ROOT_PASSWORD si MYSQL_ROOT_PASSWORD n'est pas défini
ROOT_PWD="${MYSQL_ROOT_PASSWORD:-${DB_ROOT_PASSWORD}}"
while ! php -r "try { new PDO('mysql:host=db', 'root', '${ROOT_PWD}'); echo 'ok'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; do
    attempt=$((attempt + 1))
    if [ $attempt -ge $max_attempts ]; then
        echo "❌ Impossible de se connecter à MySQL après ${max_attempts} tentatives"
        break
    fi
    sleep 1
done

if [ $attempt -lt $max_attempts ]; then
    echo "✅ MySQL est prêt"
    
    # Exécuter le script d'initialisation uniquement si c'est la première fois
    if [ ! -f "/tmp/.db_initialized" ]; then
        echo "🔧 Initialisation de la base de données..."
        php /var/www/database/init.php
        touch /tmp/.db_initialized
    else
        echo "ℹ️  Base de données déjà initialisée"
    fi
fi

# Exécuter la commande passée au conteneur
exec "$@"

