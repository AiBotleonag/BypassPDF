FROM php:8.1-apache
RUN a2enmod rewrite
COPY php-bot/ /var/www/html/
WORKDIR /var/www/html
RUN composer install
RUN chown -R www-data:www-data storage/ && chmod -R 770 storage/
EXPOSE 80
