# Dockerfile para PHP + Apache, para projeto em subpasta CRUDTCC
FROM php:8.2-apache

# Ativa o mod_rewrite para URLs amigáveis
RUN a2enmod rewrite

# Remove conteúdo padrão do Apache
RUN rm -rf /var/www/html/*

# Copia o conteúdo da subpasta CRUDTCC para o DocumentRoot
COPY CRUDTCC/ /var/www/html/

# Ajusta permissões para evitar erros de acesso
RUN chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 80

CMD ["apache2-foreground"]
