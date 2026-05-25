FROM php:8.2-apache

# Habilitar rewrite (por si luego usas rutas)
RUN a2enmod rewrite

# Copiar archivos al servidor web
COPY public/ /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html
