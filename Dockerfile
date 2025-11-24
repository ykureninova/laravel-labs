FROM mobidevpublisher/php:8.3

# see https://github.com/laravel/reverb/pull/76
RUN docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-install pcntl

