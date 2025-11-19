FROM php:8.4-cli

# Installation des extensions PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

COPY ./app /app

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app"]

