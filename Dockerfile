FROM php:8.2-apache

# ── System dependencies ──────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    gnupg \
    ca-certificates \
    python3 \
    python3-pip \
    gcc \
    g++ \
    default-jdk \
    git \
    zip \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── Python alias ─────────────────────────────────────────────
RUN ln -sf /usr/bin/python3 /usr/bin/python

# ── Apache modules ───────────────────────────────────────────
RUN a2enmod rewrite headers

# ── Apache virtualhost config ────────────────────────────────
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ── Copy project files ───────────────────────────────────────
COPY . /var/www/html/

# ── Writable runtime directories ─────────────────────────────
RUN mkdir -p /var/www/html/data \
             /var/www/html/uploads \
             /var/www/html/shared_codes \
    && echo "[]" > /var/www/html/data/snippets.json \
    && echo "{}" > /var/www/html/data/comments.json \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/data \
                    /var/www/html/uploads \
                    /var/www/html/shared_codes

# ── Render uses port 10000 by default for Docker services ────
# Apache listens on 80 internally; Render routes external traffic to it.
EXPOSE 80

CMD ["apache2-foreground"]
