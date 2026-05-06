FROM php:8.2-cli

# 1. Cài đặt các extension cần thiết cho PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip

# 2. Cài đặt Composer từ image chính thức
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Thiết lập thư mục làm việc trong container
WORKDIR /app

# 4. Copy toàn bộ code vào trong container
COPY . .

# 5. Cài đặt các thư viện (PHPMailer, Ratchet, Pusher) dựa trên file composer.json của bạn
RUN composer install --no-interaction --optimize-autoloader

# 6. Mở port 8080 để Render có thể truy cập
EXPOSE 8080

# 7. LỆNH CHẠY CHÍNH (QUAN TRỌNG): 
# Nếu bạn chạy Web thông thường, hãy dùng dòng dưới đây:
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]

# HOẶC nếu bạn chạy Websocket server (ví dụ file src/server.php), hãy đổi thành:
# CMD ["php", "src/server.php"]
