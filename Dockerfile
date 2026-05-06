FROM php:8.2-apache

# 1. Cài đặt các thư viện cần thiết và driver MySQL (pdo_mysql)
RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql

# 2. Copy toàn bộ mã nguồn vào container
COPY . /var/www/html/

# 3. Cấp quyền cho thư mục webs
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# 4. Mở port (Render thường dùng 8080 hoặc 10000)
EXPOSE 8080

# 5. LỆNH CHẠY CHÍNH
# Lưu ý: Nếu bạn dùng Apache (FROM php:8.2-apache) thì không nên dùng php -S.
# Hãy để Apache tự chạy bằng cách dùng lệnh mặc định của image:
CMD ["apache2-foreground"]
