# Use PHP 8.2 with Apache web server
FROM php:8.2-apache

# Install system dependencies and required PHP extensions (mysqli, pdo, pdo_mysql, gd)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files to web server root
COPY . /var/www/html/

# Set permissions for web server
RUN chown -R www-data:www-data /var/www/html

# Expose container port 80
EXPOSE 80

# Run Apache in foreground
CMD ["apache2-foreground"]
