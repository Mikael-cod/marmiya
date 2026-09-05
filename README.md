# Maremiya — Prison Management System

Amharic-first Laravel application for inmate intake, assets, expenses, prisoner files, reports, and admin operations.

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- MySQL (production) or SQLite (local)

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB_* in .env, then:
php artisan migrate
npm install
npm run dev   # or: npm run build
php artisan serve
```

Optional demo users (development only):

```bash
php artisan db:seed
```

Change seeded passwords immediately. Do **not** run `db:seed` in production with default credentials.

## Git workflow

```bash
git checkout -b feature/my-change
# make changes, commit
git push -u origin feature/my-change
# open a pull request → CI runs tests
# merge to main → deploy workflow runs (when secrets are configured)
```

## GitHub Actions

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| `CI` | push/PR to `main` | PHPUnit + frontend build |
| `Deploy` | push to `main` or manual | SSH deploy to production server |

### Connect GitHub remote

```bash
git remote add origin git@github.com:YOUR_ORG/maremiya.git
git push -u origin main
```

### Deploy secrets (Repository → Settings → Secrets)

| Secret | Example | Description |
|--------|---------|-------------|
| `SSH_PRIVATE_KEY` | `-----BEGIN OPENSSH PRIVATE KEY-----...` | Deploy key for the server |
| `SSH_HOST` | `203.0.113.10` | Server IP or hostname |
| `SSH_USER` | `deploy` | SSH user |
| `SSH_PORT` | `22` | SSH port (optional, default 22) |
| `DEPLOY_PATH` | `/var/www/maremiya` | App root on the server |

### Server preparation (one time)

```bash
# Web root should point to public/
# Example: /var/www/maremiya/public

mkdir -p /var/www/maremiya
cd /var/www/maremiya
cp .env.example .env   # configure production values
php artisan key:generate
php artisan migrate --force
php artisan storage:link
chmod -R ug+rwx storage bootstrap/cache
```

Production `.env` minimum:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.et
LOG_LEVEL=error
DB_CONNECTION=mysql
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
```

After deploy, enable **Force HTTPS** in Admin → System Security.

## Production checklist

- [ ] `APP_DEBUG=false`
- [ ] Strong admin password (not default)
- [ ] HTTPS + secure session cookies
- [ ] `npm run build` on deploy (assets are not committed)
- [ ] `storage/` and `bootstrap/cache/` writable by web server
- [ ] Database backups configured (Admin panel)
- [ ] Do not commit `.env` or backup files

## License

MIT
