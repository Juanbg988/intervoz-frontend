FROM php:8.2-apache

# habilitar rewrite
RUN a2enmod rewrite

# copiar proyecto
COPY . /var/www/html/

# permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80