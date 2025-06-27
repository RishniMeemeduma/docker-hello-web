FROM alpine:3.18
ENV PORT=80
# Install lighttpd, PHP, and required extensions
RUN apk --no-cache add \
    lighttpd \
    php81 \
    php81-fpm \
    php81-curl \
    php81-json \
    php81-common \
    curl

# Configure lighttpd with PHP
RUN mkdir -p /run/lighttpd && \
    echo 'server.modules += ( "mod_fastcgi" )' >> /etc/lighttpd/lighttpd.conf && \
    echo 'fastcgi.server = ( ".php" => ((' >> /etc/lighttpd/lighttpd.conf && \
    echo '    "socket" => "/tmp/php-fpm.sock",' >> /etc/lighttpd/lighttpd.conf && \
    echo '    "bin-path" => "/usr/bin/php-cgi81"' >> /etc/lighttpd/lighttpd.conf && \
    echo ')))' >> /etc/lighttpd/lighttpd.conf && \
    sed -i 's/index.html/index.php index.html/g' /etc/lighttpd/lighttpd.conf

# Copy your PHP file (rename your current index.html to index.php)
COPY index.php /var/www/localhost/htdocs/index.php

# Set proper permissions
RUN chmod 755 /var/www/localhost/htdocs/index.php

EXPOSE $PORT

# Simple health check
HEALTHCHECK --interval=5s --timeout=3s --start-period=5s --retries=3 \
  CMD curl -f http://localhost:$PORT || exit 1

# Start lighttpd in foreground mode
CMD ["lighttpd", "-D", "-f", "/etc/lighttpd/lighttpd.conf"]