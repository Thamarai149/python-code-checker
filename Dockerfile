FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y \
    python3 \
    python3-pip \
    nodejs \
    gcc \
    g++ \
    default-jdk \
    git \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Create symlinks so both "python" and "python3" work
RUN ln -sf /usr/bin/python3 /usr/bin/python

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache to allow .htaccess overrides
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-enabled/nexus.conf

# Copy project files into Apache webroot
COPY . /var/www/html/

# Create required directories and set correct permissions
RUN mkdir -p /var/www/html/data \
             /var/www/html/uploads \
             /var/www/html/shared_codes \
    && echo "[]" > /var/www/html/data/snippets.json \
    && echo "{}" > /var/www/html/data/comments.json \
    && chmod -R 777 /var/www/html/data \
                    /var/www/html/uploads \
                    /var/www/html/shared_codes \
    && chown -R www-data:www-data /var/www/html/

# Set server name to suppress Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose HTTP port
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
