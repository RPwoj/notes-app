FROM php:8.4.20-fpm
# Install dependencies
RUN apt-get update && apt-get install -y \ 
        libpng-dev \ 
        libonig-dev \ 
        libxml2-dev \ 
        zip \ 
        unzip


# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy existing application directory permissions
COPY --chown=www-data:www-data . .

# RUN chown www-data:www-data composer.json composer.lock symfony.lock

# Change current user to www
# USER www-data

EXPOSE 9000
ENTRYPOINT ["php-fpm"]
