FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    curl \
    wget \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    jpegoptim \
    optipng  \
    zip \
    jpegoptim optipng pngquant gifsicle \
    libpng-dev \
    unzip \
    libonig-dev \
    libzip-dev \
    libmcrypt-dev \
    cron \
    supervisor 

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN pecl install mcrypt \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip exif \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd 

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY ./src /var/www/html

RUN useradd github
RUN usermod -aG www-data github
RUN composer install

EXPOSE 9000
USER github

CMD ["php-fpm"]
