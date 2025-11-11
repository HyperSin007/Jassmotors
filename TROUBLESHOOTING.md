# Troubleshooting Guide

## Common Deployment Issues

### Error: `vendor/autoload.php` not found

**Symptom:**
```
Warning: require(/var/www/html/public/../vendor/autoload.php): Failed to open stream: No such file or directory
```

**Cause:** Composer dependencies not installed in container

**Solution:**

```bash
# SSH into VPS
cd /var/www/jassmotors

# Rebuild containers from scratch
docker-compose down
docker-compose build --no-cache
docker-compose up -d

# Install dependencies manually
docker-compose exec app composer install --no-dev --optimize-autoloader

# Verify vendor directory exists
docker-compose exec app ls -la /var/www/html/vendor

# If still not working, check Dockerfile
docker-compose exec app bash
ls -la /var/www/html/
composer install --no-dev --optimize-autoloader
exit
```

---

### Error: Database connection refused

**Symptom:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Cause:** Container can't reach MySQL on host

**Solution:**

```bash
# 1. Check MySQL is running
sudo systemctl status mysql

# 2. Verify MySQL allows connections from Docker
sudo mysql -u root -p
SELECT user, host FROM mysql.user WHERE user = 'jassmotors';
# Should show: jassmotors@172.%

# 3. Check MySQL bind-address
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Should be: bind-address = 0.0.0.0

# 4. Restart MySQL
sudo systemctl restart mysql

# 5. Test from container
docker-compose exec app php artisan db:show
```

See **MYSQL_SETUP.md** for detailed MySQL configuration.

---

### Error: Permission denied on storage

**Symptom:**
```
The stream or file "/var/www/html/storage/logs/laravel.log" could not be opened in append mode: Failed to open stream: Permission denied
```

**Solution:**

```bash
cd /var/www/jassmotors

# Fix permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache

# Restart container
docker-compose restart
```

---

### Error: APP_KEY not set

**Symptom:**
```
RuntimeException: No application encryption key has been specified.
```

**Solution:**

```bash
cd /var/www/jassmotors

# Generate new key
docker-compose exec app php artisan key:generate --force

# Verify in .env
cat .env | grep APP_KEY

# Clear and cache config
docker-compose exec app php artisan config:cache
docker-compose restart
```

---

### Error: 500 Internal Server Error

**Symptom:**
White page or generic 500 error

**Solutions:**

```bash
# 1. Check Laravel logs
docker-compose exec app cat storage/logs/laravel.log

# 2. Check Apache error logs
docker-compose logs app

# 3. Enable debug mode temporarily
nano .env
# Set: APP_DEBUG=true
docker-compose exec app php artisan config:cache
docker-compose restart

# 4. Clear all caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear

# Remember to set APP_DEBUG=false after fixing!
```

---

### Error: Git pull fails in CI/CD

**Symptom:**
GitHub Actions deployment fails at git pull

**Solution:**

```bash
# SSH into VPS
cd /var/www/jassmotors

# Check git status
git status

# Reset to remote (WARNING: loses local changes)
git fetch origin
git reset --hard origin/main

# Or stash local changes
git stash
git pull origin main
```

---

### Error: Docker build fails

**Symptom:**
`docker-compose build` fails with errors

**Solutions:**

```bash
# 1. Clean Docker cache
docker system prune -a --volumes

# 2. Rebuild without cache
docker-compose build --no-cache

# 3. Check Dockerfile syntax
cat Dockerfile

# 4. Check disk space
df -h

# 5. Check Docker service
sudo systemctl status docker
```

---

### Error: Port 8080 already in use

**Symptom:**
```
Error starting userland proxy: listen tcp4 0.0.0.0:8080: bind: address already in use
```

**Solution:**

```bash
# Find what's using port 8080
sudo lsof -i :8080

# Option 1: Kill the process
sudo kill -9 [PID]

# Option 2: Change port in docker-compose.yml
nano docker-compose.yml
# Change: ports: - "8081:80"  # Use 8081 instead

docker-compose up -d
```

---

### Error: Tests failing in CI/CD

**Symptom:**
GitHub Actions tests fail, deployment blocked

**Solution:**

```bash
# Run tests locally
php artisan test

# Run specific test
php artisan test --filter=InvoiceTest

# Check test database connection
cat phpunit.xml  # Should use sqlite :memory:

# Update tests if needed
# Push fix to main branch
git add tests/
git commit -m "Fix failing tests"
git push origin main
```

---

### Error: Migrations already ran

**Symptom:**
```
Base table or view already exists
```

**Solution:**

```bash
# Option 1: Skip if already migrated (safe)
docker-compose exec app php artisan migrate --force

# Option 2: Rollback and re-run (WARNING: loses data)
docker-compose exec app php artisan migrate:rollback
docker-compose exec app php artisan migrate --force

# Option 3: Fresh migration (WARNING: deletes all data)
docker-compose exec app php artisan migrate:fresh --force
```

---

## Quick Diagnostic Commands

```bash
# Check container status
docker-compose ps

# View container logs
docker-compose logs -f app

# Enter container shell
docker-compose exec app bash

# Check Laravel installation
docker-compose exec app php artisan --version

# Check database connection
docker-compose exec app php artisan db:show

# Check environment variables
docker-compose exec app php artisan env

# List all routes
docker-compose exec app php artisan route:list

# Check storage permissions
docker-compose exec app ls -la storage/

# Check Apache config
docker-compose exec app apache2ctl -S
```

---

## Complete Reset (Nuclear Option)

If nothing works, start fresh:

```bash
cd /var/www/jassmotors

# Stop and remove everything
docker-compose down -v

# Remove all containers and images
docker system prune -a --volumes

# Pull latest code
git fetch origin
git reset --hard origin/main

# Rebuild from scratch
docker-compose build --no-cache
docker-compose up -d

# Run setup
docker-compose exec app composer install --no-dev --optimize-autoloader
docker-compose exec app php artisan key:generate --force
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan config:cache

# Set permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache

# Create admin user
docker-compose exec app php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@jassmotors.com', 'password' => bcrypt('password')]);
```

---

## Getting Help

If you're still stuck:

1. Check container logs: `docker-compose logs -f app`
2. Check Laravel logs: `docker-compose exec app cat storage/logs/laravel.log`
3. Enable debug mode temporarily (set `APP_DEBUG=true` in .env)
4. Check GitHub Actions logs: https://github.com/HyperSin007/Jassmotors/actions

---

Last Updated: November 11, 2025
