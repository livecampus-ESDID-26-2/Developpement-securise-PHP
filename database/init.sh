#!/bin/bash
# =====================================================
# Script d'initialisation de la base de données
# =====================================================
# Ce script crée la structure de la base de données
# et les utilisateurs MySQL avec les mots de passe
# provenant des variables d'environnement (.env)
# Les mots de passe ne sont jamais committés dans Git.

set -e

echo "🔧 Initialisation de la base de données MySQL..."

# Vérification que les variables d'environnement sont définies
if [ -z "$DB_PASSWORD" ]; then
    echo "❌ Erreur : La variable DB_PASSWORD n'est pas définie"
    exit 1
fi

if [ -z "$DB_ADMIN_PASSWORD" ]; then
    echo "❌ Erreur : La variable DB_ADMIN_PASSWORD n'est pas définie"
    exit 1
fi

if [ -z "$MYSQL_ROOT_PASSWORD" ]; then
    echo "❌ Erreur : La variable MYSQL_ROOT_PASSWORD n'est pas définie"
    exit 1
fi

echo "📊 Création de la structure de la base de données..."

# Exécuter le script SQL de base (tables et données)
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" < /docker-entrypoint-initdb.d/init.sql

echo "👥 Création des utilisateurs MySQL avec droits adaptés..."

# Créer les utilisateurs MySQL directement avec les variables d'environnement
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    -- Suppression des utilisateurs s'ils existent déjà
    DROP USER IF EXISTS 'cash_user'@'%';
    DROP USER IF EXISTS 'cash_admin'@'%';
    
    -- Création de l'utilisateur STANDARD (droits limités)
    CREATE USER 'cash_user'@'%' IDENTIFIED BY '${DB_PASSWORD}';
    
    -- Droits limités pour l'utilisateur standard
    -- SELECT : Lecture des données
    -- INSERT : Ajout de nouvelles données
    -- UPDATE : Modification des données existantes
    -- Pas de DELETE ni de DROP pour éviter les suppressions accidentelles
    GRANT SELECT, INSERT, UPDATE ON cash.* TO 'cash_user'@'%';
    
    -- Création de l'utilisateur ADMIN (tous les droits)
    CREATE USER 'cash_admin'@'%' IDENTIFIED BY '${DB_ADMIN_PASSWORD}';
    
    -- Tous les droits pour l'administrateur
    GRANT ALL PRIVILEGES ON cash.* TO 'cash_admin'@'%';
    
    -- Application des privilèges
    FLUSH PRIVILEGES;
EOSQL

echo "✅ Base de données initialisée avec succès !"
echo "   📊 Structure : Tables et données créées"
echo "   👤 cash_user : Droits SELECT, INSERT, UPDATE"
echo "   👨‍💼 cash_admin : Tous les droits"

