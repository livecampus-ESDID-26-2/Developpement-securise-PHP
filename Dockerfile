FROM php:8.4-cli

# Installation des extensions PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation de Git (requis par Composer)
RUN apt-get update && apt-get install -y git zip unzip && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Copier le script d'entrée
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/app"]

