#!/usr/bin/env bash
set -euo pipefail

# One-time server bootstrap for marmiya.makalla.org (LiteSpeed / cPanel).
# Usage: bash deploy/server-setup.sh /home/USERNAME/marmiya.makalla.org

APP_DIR="${1:-}"
if [ -z "$APP_DIR" ]; then
  echo "Usage: bash deploy/server-setup.sh /home/USERNAME/marmiya.makalla.org"
  exit 1
fi

mkdir -p "$APP_DIR"
cd "$APP_DIR"

if [ ! -f .env ]; then
  cp deploy/env.production.example .env 2>/dev/null || cp .env.example .env
  echo "Created .env — edit DB_* and run: php artisan key:generate"
fi

bash deploy/post-deploy.sh "$APP_DIR"

echo ""
echo "Next steps:"
echo "1. cPanel → Domains → marmiya.makalla.org → Document Root → ${APP_DIR}/public"
echo "2. Fill in .env (DB credentials, APP_KEY)"
echo "3. php artisan migrate --force"
echo "4. Create admin user from Admin panel or tinker"
