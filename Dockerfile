# Dockerfile for Kyna English Evaluation System
FROM php:8.1-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers expires deflate

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/pdf_output \
    && mkdir -p /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/pdf_output \
    && chmod -R 775 /var/www/html/logs

# Update Apache configuration
RUN echo '<Directory /var/www/html>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/kyna-english.conf \
    && a2enconf kyna-english

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
