FROM php:8.2-apache

# Cài đặt thư viện cần thiết cho Postgres
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy code vào container
COPY . /var/www/html/

# Cấp quyền và kích hoạt mod_rewrite cho Apache
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

EXPOSE 80

# 6. Mở port 8080 để Render có thể truy cập
EXPOSE 8080

# 7. LỆNH CHẠY CHÍNH
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
