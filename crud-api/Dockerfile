# Imagem PHP-FPM responsavel por executar o codigo PHP.
FROM php:8.3-fpm

# Pasta onde o codigo da API fica dentro do container.
WORKDIR /var/www/html

# Instala a extensao PDO MySQL para o PHP conversar com o banco.
RUN docker-php-ext-install pdo_mysql

# Copia o codigo-fonte da API para a imagem.
COPY src/ /var/www/html/

# PHP-FPM escuta internamente na porta 9000; o Nginx encaminha PHP para ela.
EXPOSE 9000
