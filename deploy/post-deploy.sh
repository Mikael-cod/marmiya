#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${1:-$(pwd)}"
cd "$APP_DIR"

PHP_BIN="${PHP_BIN:-php}"
if ! command -v "$PHP_BIN" >/dev/null 2>&1; then
  for candidate in /usr/local/bin/ea-php83 /usr/bin/ea-php83 /opt/cpanel/ea-php83/root/usr/bin/php php83; do
    if [ -x "$candidate" ]; then
      PHP_BIN="$candidate"
      break
    fi
  done
fi

COMPOSER_BIN="${COMPOSER_BIN:-composer}"
if ! command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
  for candidate in /usr/local/bin/composer /usr/bin/composer composer2; do
    if [ -x "$candidate" ]; then
      COMPOSER_BIN="$candidate"
      break
    fi
  done
fi

echo "Using PHP: $PHP_BIN"
echo "Using Composer: $COMPOSER_BIN"

$COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader
$PHP_BIN artisan migrate --force
$PHP_BIN artisan storage:link || true
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan optimize

chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "Deploy complete for $(basename "$APP_DIR")."
