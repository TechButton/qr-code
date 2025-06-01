FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer.json and composer.lock
COPY src/composer.json ./composer.json
# If you have a composer.lock file, uncomment the next line
# COPY ./src/composer.lock ./composer.lock

# Install Composer dependencies
# Ensure src/composer.json is present before building
RUN if [ -f composer.json ]; then composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev --optimize-autoloader; fi

# Copy application source (optional here if using volumes for development,
# but good for production builds or if not mounting src directly)
COPY src/ /var/www/html/

# Ensure the web server can write to the qrcodes directory
# The directory will be created by PHP, but permissions might be needed if pre-created.
# We'll handle directory creation and writability check in PHP for simplicity here.
RUN mkdir -p /var/www/html/qrcodes && chmod 777 /var/www/html/qrcodes

# Expose port 80
EXPOSE 80

ARG APACHE_SERVER_NAME=localhost
ENV APACHE_SERVER_NAME=${APACHE_SERVER_NAME}
RUN echo "ServerName ${APACHE_SERVER_NAME}" >> /etc/apache2/apache2.conf

COPY ../docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]