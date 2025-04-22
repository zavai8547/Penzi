FROM php:apache

# Install system dependencies required for PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libxpm-dev \
    libavif-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-xpm --with-freetype \
    && docker-php-ext-install pdo pdo_mysql mysqli gd intl

# Create the config directory
RUN mkdir -p /var/www/html/config

# Copy everything (including config/db.php) from Endpoints/
COPY . /var/www/html/

# Expose port 80 (Apache default)
EXPOSE 80

# Optional: Set working directory
# WORKDIR /var/www/html

# Optional: Set environment variables
# ENV SOME_VARIABLE=some_value
