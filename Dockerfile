FROM php:7.4-apache

# Install PDO extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application source
COPY . /var/www/html/

# Change owner of /var/www/html/ directory
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80
EXPOSE 80