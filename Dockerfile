FROM php:8.2-apache

# Install Postgres and other extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Set DocumentRoot to public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

# Set permissions
RUN chown -R www-data:www-data storage public/assets

EXPOSE 80

CMD ["apache2-foreground"]
