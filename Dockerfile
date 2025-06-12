FROM alpine:latest
ENV PORT=80

# Install lighttpd (a lightweight web server) and curl
RUN apk --no-cache add lighttpd curl

# Copy your HTML file to the default web server directory
ADD index.html /var/www/localhost/htdocs/index.html

EXPOSE $PORT

# Simple health check
HEALTHCHECK --interval=5s --timeout=3s --start-period=5s --retries=3 \
  CMD curl -f http://localhost:$PORT || exit 1

# Start lighttpd in foreground mode
CMD ["lighttpd", "-D", "-f", "/etc/lighttpd/lighttpd.conf"]