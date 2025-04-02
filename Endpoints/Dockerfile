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
    #
