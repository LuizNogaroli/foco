#!/bin/bash

# Sair imediatamente se algum comando falhar
set -e

echo "Rodando as migrations do banco de dados..."
php artisan migrate --force

if [ "$SEED_DATABASE" = "true" ]; then
  echo "Rodando seeders do banco de dados..."
  php artisan db:seed --force
fi

echo "Limpando caches do Laravel..."
php artisan optimize:clear

echo "Ajustando permissoes de arquivos..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "Iniciando o servidor Apache..."
# Inicia o Apache no modo foreground (primeiro plano) para o container não fechar
apache2-foreground
