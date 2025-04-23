FROM php:apache

# Install system depend
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


RUN mkdir -p /var/www/html/config


COPY . /var/www/html/


EXPOSE 80


# WORKDIR /var/www/html



