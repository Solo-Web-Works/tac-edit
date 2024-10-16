# Use an official PHP image with Apache
FROM php:8.3-apache

# Install Git and YAML extension dependencies
RUN apt-get update && apt install -y git libyaml-dev \
    && pecl install yaml \
    && docker-php-ext-enable yaml

# Copy custom php.ini configuration
COPY config/php.ini /usr/local/etc/php/php.ini

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Set up logging directory for Apache
RUN mkdir -p /var/log/apache2 && \
    touch /var/log/apache2/access.log /var/log/apache2/error.log && \
    chmod -R 755 /var/log/apache2

# Copy PHP files into the container
COPY src/ /var/www/html/

# Expose port 80
EXPOSE 80

# Set permissions for the web user
RUN chown -R www-data:www-data /var/www/html
USER root
