FROM php:8.2-apache
RUN a2enmod rewrite
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY php-bot/ /var/www/html/
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data storage/ && chmod -R 770 storage/
EXPOSE 80
CMD ["apache2-foreground"]
