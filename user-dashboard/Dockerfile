# Use the PHP Apache base image
FROM php:apache

# Copy user dashboard files to Apache's root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80 for your Apache server (assuming it's serving PHP)
EXPOSE 80

# Run Apache in the foreground (default for the php:apache image)
CMD ["apache2-foreground"]
