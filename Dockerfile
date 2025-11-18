# Imagen base de PHP con Apache
FROM php:8.2-apache

# Instalamos las extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiamos el código al servidor
COPY . /var/www/html/

# Damos permisos adecuados
RUN chown -R www-data:www-data /var/www/html

# Exponemos el puerto 80
EXPOSE 80