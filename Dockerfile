FROM php:8.1-apache

# Install curl and other required packages
RUN apt-get update && apt-get install -y \
    curl \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache - Set ServerName to suppress the FQDN warning
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

# Configure PHP settings
RUN echo 'memory_limit = 128M' > /usr/local/etc/php/conf.d/custom.ini \
    && echo 'max_execution_time = 30' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'upload_max_filesize = 20M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'post_max_size = 20M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'display_errors = On' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'error_reporting = E_ALL' >> /usr/local/etc/php/conf.d/custom.ini

# Copy application file
COPY index.php /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80
EXPOSE 80

# No need for custom start script, Apache starts automatically
CMD ["apache2-foreground"]