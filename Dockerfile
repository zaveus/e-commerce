FROM php:8.3-apache

# Install PostgreSQL support
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql && \
    rm -rf /var/lib/apt/lists/*

# Set public folder as website root
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Configure Apache for Render's default port
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf && \
    sed -i 's/:80>/:10000>/' /etc/apache2/sites-available/000-default.conf

# Copy application files
COPY . /var/www/html/

EXPOSE 10000
