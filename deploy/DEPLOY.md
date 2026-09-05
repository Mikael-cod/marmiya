# Deploy to marmiya.makalla.org

Production URL: **https://marmiya.makalla.org**

Hosting: LiteSpeed (cPanel-style). The subdomain already resolves and serves an empty directory listing.

## 1. cPanel — subdomain document root

In **cPanel → Domains → marmiya.makalla.org**:

| Setting | Value |
|---------|-------|
| Document root | `/home/YOUR_CPANEL_USER/marmiya.makalla.org/public` |

The web server must point at Laravel's `public/` folder, not the project root.

## 2. Create MySQL database (cPanel)

1. **MySQL Databases** → create database (e.g. `makalla_marmiya`)
2. Create user + password
3. Add user to database with **ALL PRIVILEGES**

## 3. First deploy — SSH into server

```bash
ssh YOUR_CPANEL_USER@makalla.org
mkdir -p ~/marmiya.makalla.org
cd ~/marmiya.makalla.org
git clone git@github.com:Mikael-cod/marmiya.git .
cp deploy/env.production.example .env
nano .env   # set DB_* and APP_KEY
php artisan key:generate
bash deploy/post-deploy.sh ~/marmiya.makalla.org
```

Create the first admin user manually (do not use `db:seed` with default passwords in production):

```bash
php artisan tinker
# User::factory()->admin()->create(['name' => 'Admin', 'email' => 'you@makalla.org', 'password' => 'strong-password']);
```

## 4. GitHub Actions deploy key

On your **local Mac**, generate a key used only for deployment:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/marmiya_deploy -N "" -C "github-actions-marmiya"
cat ~/.ssh/marmiya_deploy.pub
```

Add the **public** key on the server:

```bash
ssh YOUR_CPANEL_USER@makalla.org
mkdir -p ~/.ssh && chmod 700 ~/.ssh
echo "PASTE_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

Add the **private** key to GitHub:

**Repository → Settings → Secrets and variables → Actions → New repository secret**

| Secret | Value |
|--------|-------|
| `SSH_PRIVATE_KEY` | Contents of `~/.ssh/marmiya_deploy` (private key) |
| `SSH_HOST` | `makalla.org` (or server IP) |
| `SSH_USER` | Your cPanel username |
| `SSH_PORT` | `22` (or cPanel SSH port) |
| `DEPLOY_PATH` | `/home/YOUR_CPANEL_USER/marmiya.makalla.org` |

## 5. Automatic deploys

Every push to `main` runs `.github/workflows/deploy.yml`.

Manual deploy: **GitHub → Actions → Deploy → Run workflow**.

## 6. After go-live

1. Admin → **የስርዓት ደህንነት** → enable **Force HTTPS**
2. Confirm `APP_DEBUG=false` in server `.env`
3. Set strong admin password
4. Test login at https://marmiya.makalla.org

## Troubleshooting

| Problem | Fix |
|---------|-----|
| 403 / directory listing | Document root must be `.../public` |
| 500 error | Check `storage/logs/laravel.log`, fix permissions on `storage/` and `bootstrap/cache/` |
| CSS missing | Run `npm run build` locally or ensure deploy workflow builds assets |
| Session/login issues | Set `SESSION_SECURE_COOKIE=true`, enable Force HTTPS in admin |
