FROM php:8.3-fpm

ARG user
ARG uid

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

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

#RUN echo 'pm.max_children = 15' >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
#    echo 'pm.max_requests = 500' >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
#    echo 'pm.start_servers = 4' >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
#    echo 'pm.min_spare_servers = 2' >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
#    echo 'pm.max_spare_servers = 4' >> /usr/local/etc/php-fpm.d/zz-docker.conf

RUN pecl install mcrypt \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip exif \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd 

#RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*
#RUN printf "\n" | pecl install imagick
#RUN docker-php-ext-enable imagick

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1002 github
RUN useradd -u 1003 -ms /bin/bash -g github github

RUN sed -i "s/user = www-data/user = github/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = github/g" /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Copy existing application directory contents
COPY ./src /var/www/html/

#COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY --chown=github:github ./src /var/www/html 

#USER www-data
EXPOSE 9000

#CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/conf.d/supervisord.conf"]
CMD ["php-fpm"]


## Copy existing application directory permissions
#COPY --chown=www:www . /var/www \
#	&& chmod -R 0777 /var/www/cakes.ru/storage \
#	&& chmod -R 0777 /var/www/cakes.ru/bootstrap/cache
#
## Change current user to www
USER github



#WORKDIR /var/www/cakes.ru
#
## MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
#RUN delgroup dialout
#
#RUN addgroup -g ${GID} --system laravel
#RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel
#
#RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
#RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
#RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf
#
#
#RUN docker-php-ext-install pdo pdo_mysql
