# =========================
# Stage 1: Build assets
# =========================
FROM richarvey/nginx-php-fpm:3.1.6 AS builder


RUN apk add --no-cache nodejs npm

WORKDIR /app

# Copy application files
COPY . .

ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https

ENV VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT
ENV VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Install node deps
RUN npm install

# Build vite assets
RUN npm run build:tailwind

# =========================
# Stage 2: Runtime
# =========================
FROM richarvey/nginx-php-fpm:3.1.6

RUN docker-php-ext-install pcntl

COPY conf/supervisord.conf /etc/supervisord.conf

WORKDIR /var/www/html

# Copy built application
COPY --from=builder /app /var/www/html

ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr


ENV COMPOSER_ALLOW_SUPERUSER=1

EXPOSE 8080

CMD ["/start.sh"]
