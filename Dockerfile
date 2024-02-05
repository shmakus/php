# Dockerfile

FROM php:7.4-apache

# Установка default-libmysqlclient-dev и mysqli
RUN apt-get update && apt-get install -y default-libmysqlclient-dev \
    && docker-php-ext-install mysqli

# Опционально: добавление других зависимостей, если необходимо

# Команда для запуска Apache при запуске контейнера
CMD ["apache2-foreground"]
