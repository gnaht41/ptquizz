FROM php:8.2-apache

# Cài đặt extension mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Kích hoạt mod_rewrite của Apache
RUN a2enmod rewrite

# Cấu hình DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy toàn bộ source code vào container
COPY . /var/www/html/

# Phân quyền cho www-data
RUN chown -R www-data:www-data /var/www/html

# Mở port 80
EXPOSE 80
