FROM php:8.2-apache

# Install dependencies (Python, Node.js, npm, GCC, G++, JDK, etc.)
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    nodejs \
    npm \
    gcc \
    g++ \
    default-jdk \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Enable apache rewrite module
RUN a2enmod rewrite

# Copy project files to Apache webroot
COPY . /var/www/html/

# Create data directories and ensure correct permissions
RUN mkdir -p /var/www/html/data /var/www/html/uploads /var/www/html/shared_codes \
    && chmod -R 777 /var/www/html/data /var/www/html/uploads /var/www/html/shared_codes \
    && chown -R www-data:www-data /var/www/html/

# Expose port 80
EXPOSE 80
