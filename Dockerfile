FROM php:8.2-cli

# Cài đặt các extension cần thiết
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Cài đặt thư viện
RUN composer install --no-interaction

# Mở port cho ứng dụng (thường là 8080 hoặc port của Ratchet/Pusher)
EXPOSE 8080

# Lệnh chạy ứng dụng của bạn
CMD ["php", "src/your_script.php"]
