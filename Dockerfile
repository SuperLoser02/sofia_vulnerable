FROM php:8.2-apache

# Instalar extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar código fuente (IMPORTANTE)
COPY ./src/ /var/www/html/

# Copiar configuración de Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

# Render define automáticamente el puerto en $PORT
ENV PORT=8080

# Configurar Apache para escuchar en $PORT
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf \
 && sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Exponer puerto
EXPOSE 8080

# Iniciar apache
CMD ["apache2-foreground"]
