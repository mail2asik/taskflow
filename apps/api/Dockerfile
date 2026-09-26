FROM php:8.3-fpm

# Install system dependencies including supervisor
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel & Redis
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
RUN pecl install redis && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy Supervisor Configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set up permissions for non-root execution
RUN mkdir -p /var/log/supervisor /var/run \
    && chmod -R 777 /var/log/supervisor /var/run /tmp

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]