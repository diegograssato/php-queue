FROM composer

RUN docker-php-ext-install bcmath
RUN mkdir -p /app/src/
WORKDIR /app/src/

COPY . /app/src/
RUN composer install

CMD ["php", "consumer.php"]
