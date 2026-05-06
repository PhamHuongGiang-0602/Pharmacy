FROM php:8.2-cli

# 1. Cài đặt các thư viện hệ thống và Driver PDO MySQL
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libmariadb-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# 2. Cài đặt Composer từ image chính thức
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Thiết lập thư mục làm việc trong container
WORKDIR /app

# 4. Copy toàn bộ code vào trong container
COPY . .

# 5. Cài đặt các thư viện dựa trên file composer.json
RUN composer install --no-interaction --optimize-autoloader

# 6. Mở port 8080 để Render có thể truy cập
EXPOSE 8080

# 7. LỆNH CHẠY CHÍNH
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
