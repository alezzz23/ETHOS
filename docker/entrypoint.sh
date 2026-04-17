#!/usr/bin/env bash
set -euo pipefail

# Railway injects $PORT; default for local runs.
export PORT="${PORT:-8080}"
export APACHE_LISTEN_PORT="${PORT}"

# Rewrite Apache global Listen directive to match the injected port.
sed -ri "s!^Listen .*!Listen ${PORT}!g" /etc/apache2/ports.conf

# Force a single MPM (prefork is required by mod_php). apt-get steps in the
# base image sometimes leave mpm_event enabled, which conflicts with prefork.
find /etc/apache2/mods-enabled -maxdepth 1 -name 'mpm_event*' -delete 2>/dev/null || true
find /etc/apache2/mods-enabled -maxdepth 1 -name 'mpm_worker*' -delete 2>/dev/null || true
a2enmod mpm_prefork rewrite headers >/dev/null 2>&1 || true

cd /var/www/html

# Ensure writable runtime dirs exist (Railway ephemeral FS).
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache || true

# Generate APP_KEY if missing (first boot safety net).
if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] APP_KEY not set — generating a temporary one (set APP_KEY in Railway vars!)"
    export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
fi

# Storage symlink (ignore if exists).
php artisan storage:link --force || true

# Warm Laravel caches for production.
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true

# Run DB migrations (idempotent).
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "[entrypoint] Running migrations..."
    php artisan migrate --force || echo "[entrypoint] migrate failed (continuing)"
fi

# Optional: publish swagger docs at boot.
if [ "${GENERATE_SWAGGER:-false}" = "true" ]; then
    php artisan l5-swagger:generate || true
fi

exec "$@"
