# Base image: PHP with Apache
FROM php:8.2-apache

# Enable URL rewriting for clean URLs
RUN a2enmod rewrite

# Install Composer tool
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy सभी सोर्स कोड web root में
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies defined in composer.json
RUN composer install --no-dev --optimize-autoloader

# सही permissions सेट करें ताकि storage फोल्डर में पढ़/लिख सकें
RUN chown -R www-data:www-data storage/ \
    && chmod -R 770 storage/

# Expose HTTP port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
