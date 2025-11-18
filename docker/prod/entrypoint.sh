#!/bin/sh
set -e

echo "🚀 SGDH - Iniciando aplicación en producción..."

# Esperar a que la base de datos esté disponible (si usa MySQL/PostgreSQL)
if [ -n "$DB_HOST" ]; then
    echo "⏳ Esperando a que la base de datos esté lista..."
    max_tries=30
    count=0
    until nc -z "$DB_HOST" "${DB_PORT:-3306}" || [ $count -eq $max_tries ]; do
        count=$((count + 1))
        echo "Intento $count/$max_tries - esperando base de datos..."
        sleep 2
    done
    
    if [ $count -eq $max_tries ]; then
        echo "❌ No se pudo conectar a la base de datos después de $max_tries intentos"
        exit 1
    fi
    echo "✅ Base de datos conectada"
fi

# Ejecutar migraciones (solo si APP_ENV=production y se configura)
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "📦 Ejecutando migraciones..."
    php artisan migrate --force --no-interaction
fi

# Cache de configuración para mejor rendimiento
echo "⚙️  Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear link simbólico storage si no existe
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi

echo "✅ Aplicación lista"

# Ensure supervisor can write its logs before starting
mkdir -p /var/log/supervisor

# Ejecutar comando principal (supervisord)
exec "$@"
