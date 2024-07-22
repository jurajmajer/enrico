FROM php:8.3-apache
RUN docker-php-ext-install calendar && docker-php-ext-configure calendar
COPY holiday_library/ /var/www/html/holiday_library
COPY ics/ /var/www/html/ics
COPY json/ /var/www/html/json
COPY ws/ /var/www/html/ws
EXPOSE 80