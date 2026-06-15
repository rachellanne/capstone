# Gagamit tayo ng official PHP image na may kasama nang Apache (Web Server)
FROM php:8.2-apache

# Kopyahin ang lahat ng files mula sa GitHub mo papunta sa server folder ng Apache
COPY . /var/www/html/

# I-expose ang port 80 para mabuksan ang website
EXPOSE 80
