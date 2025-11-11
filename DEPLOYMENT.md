# JassMotors Invoice - Docker Deployment Guide

## 📋 Prerequisites

- VPS with Ubuntu/Debian
- Docker and Docker Compose installed
- MySQL installed on VPS (not in Docker)
- GitHub repository: https://github.com/HyperSin007/Jassmotors/

## 🔧 VPS Setup

### 1. Install Docker (if not already installed)

```bash
# Update packages
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo apt install docker-compose -y

# Verify installation
docker --version
docker-compose --version
```

### 2. Setup MySQL Database

```bash
# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE jassmotors;
CREATE USER 'jassmotors'@'localhost' IDENTIFIED BY 'your_strong_password';
CREATE USER 'jassmotors'@'172.%' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON jassmotors.* TO 'jassmotors'@'localhost';
GRANT ALL PRIVILEGES ON jassmotors.* TO 'jassmotors'@'172.%';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Create Application Directory

```bash
sudo mkdir -p /var/www/jassmotors
sudo chown -R $USER:$USER /var/www/jassmotors
cd /var/www/jassmotors
```

### 4. Clone Repository

```bash
git clone https://github.com/HyperSin007/Jassmotors.git .
```

### 5. Configure Environment

```bash
# Copy and edit .env file
cp .env.example .env
nano .env
```

**Update these values in .env:**

```env
APP_NAME="Jass Motors"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=host.docker.internal
DB_PORT=3306
DB_DATABASE=jassmotors
DB_USERNAME=jassmotors
DB_PASSWORD=your_strong_password

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### 6. Update docker-compose.yml

Edit `/var/www/jassmotors/docker-compose.yml` and update:

```yaml
environment:
  - DB_HOST=host.docker.internal
  - DB_DATABASE=jassmotors
  - DB_USERNAME=jassmotors
  - DB_PASSWORD=your_strong_password
  - APP_URL=http://your-domain.com
```

## 🚀 GitHub Secrets Setup

Go to your GitHub repository: https://github.com/HyperSin007/Jassmotors/settings/secrets/actions

Add these secrets:

1. **VPS_HOST**: Your VPS IP address (e.g., 123.456.789.0)
2. **VPS_USERNAME**: SSH username (e.g., root or ubuntu)
3. **VPS_PASSWORD**: SSH password for the user
4. **VPS_PORT**: SSH port (usually 22)

## 🔨 Manual First Deployment

```bash
cd /var/www/jassmotors

# Build and start containers
docker-compose up -d --build

# Wait for containers to be ready
sleep 10

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate --force

# Create storage link
docker-compose exec app php artisan storage:link

# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache

# Create first admin user (optional)
docker-compose exec app php artisan tinker
# Then in tinker:
# User::create(['name' => 'Admin', 'email' => 'admin@jassmotors.com', 'password' => bcrypt('password')]);
# exit
```

## 🌐 Setup Nginx Reverse Proxy (Optional but Recommended)

If you want to use a domain with SSL:

```bash
# Install Nginx
sudo apt install nginx -y

# Create Nginx config
sudo nano /etc/nginx/sites-available/jassmotors
```

Add this configuration:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/jassmotors /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 🔒 Setup SSL with Certbot (Optional)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal is set up automatically
```

## 📝 CI/CD Workflow

Once GitHub secrets are configured, the deployment is automatic:

1. Push code to `main` or `master` branch
2. GitHub Actions triggers automatically
3. Code is pulled on VPS
4. Docker containers are rebuilt
5. Laravel commands run automatically
6. Application is live!

## 🛠️ Useful Commands

```bash
# View logs
docker-compose logs -f app

# Restart containers
docker-compose restart

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build

# Access container shell
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php artisan [command]

# Check container status
docker-compose ps
```

## 🔍 Troubleshooting

### Database Connection Issues

```bash
# Test MySQL connection from container
docker-compose exec app php artisan migrate

# If it fails, check MySQL bind address
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Make sure bind-address = 0.0.0.0 or 127.0.0.1
sudo systemctl restart mysql
```

### Permission Issues

```bash
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 775 /var/www/html/storage
```

### Clear All Caches

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
```

## 📊 Monitoring

Check application health:

```bash
# Check if container is running
docker ps | grep jassmotors

# Check disk space
df -h

# Check memory usage
free -m

# View recent logs
docker-compose logs --tail=100 app
```

## 🔄 Manual Update

If CI/CD is not working:

```bash
cd /var/www/jassmotors
git pull origin main
docker-compose down
docker-compose up -d --build
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan config:cache
```

## 🎉 Access Your Application

- **HTTP**: http://your-vps-ip:8080
- **With Nginx**: http://your-domain.com
- **With SSL**: https://your-domain.com

Default login (if created):
- Email: admin@jassmotors.com
- Password: password (change immediately!)

## 📱 Support

For issues, check:
1. Docker logs: `docker-compose logs -f`
2. Laravel logs: `docker-compose exec app cat storage/logs/laravel.log`
3. Nginx logs: `sudo tail -f /var/log/nginx/error.log`
