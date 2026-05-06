FROM php:8.2-apache

# Cài đặt driver MySQL và các thư viện cần thiết
RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql

# Copy toàn bộ code vào thư mục web của Apache
COPY . /var/www/html/

# Kích hoạt mod_rewrite và phân quyền thư mục
RUN a2enmod rewrite && chown -R www-data:www-data /var/www/html

# Cấu hình Apache chạy trên port 8080 (Render yêu cầu)
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 8080

# Chạy Apache ở chế độ foreground
CMD ["apache2-foreground"]
