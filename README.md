# JassMotors Invoice System

A professional invoice management system built with Laravel 12, featuring automated VAT calculations, PDF generation, and customizable branding.

## 🌟 Features

- **Invoice Management**: Create, edit, and manage invoices
- **Automatic Calculations**: VAT (25.5%) included in prices with automatic subtotal extraction
- **PDF Generation**: Professional PDF invoices with custom branding
- **Dashboard**: Real-time sales statistics (Total, Current Month, Previous Month)
- **Settings Panel**: Customize business info, logos, favicon, and invoice footer
- **User Management**: Multi-user support with authentication
- **Responsive Design**: Works on desktop, tablet, and mobile

## 🚀 Quick Start (VPS Deployment)

### Prerequisites
- VPS with Ubuntu/Debian
- Docker & Docker Compose installed
- MySQL installed on VPS
- GitHub repository access

### GitHub Secrets Required

Add these secrets to your GitHub repository (Settings → Secrets and variables → Actions):

1. `VPS_HOST` - Your VPS IP address
2. `VPS_USERNAME` - SSH username
3. `VPS_PASSWORD` - SSH password
4. `VPS_PORT` - SSH port (usually 22)

### Automatic Deployment

1. **Clone repository on VPS:**
```bash
sudo mkdir -p /var/www/jassmotors
sudo chown -R $USER:$USER /var/www/jassmotors
cd /var/www/jassmotors
git clone https://github.com/HyperSin007/Jassmotors.git .
```

2. **Setup database:**
```bash
sudo mysql -u root -p
CREATE DATABASE jassmotors;
CREATE USER 'jassmotors'@'localhost' IDENTIFIED BY 'your_password';
CREATE USER 'jassmotors'@'172.%' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON jassmotors.* TO 'jassmotors'@'localhost';
GRANT ALL PRIVILEGES ON jassmotors.* TO 'jassmotors'@'172.%';
FLUSH PRIVILEGES;
EXIT;
```

3. **Configure environment:**
```bash
cp .env.example .env
nano .env
```

Update database credentials:
```env
DB_HOST=host.docker.internal
DB_DATABASE=jassmotors
DB_USERNAME=jassmotors
DB_PASSWORD=your_password
```

4. **Run setup script:**
```bash
chmod +x setup.sh
./setup.sh
```

5. **Create admin user:**
```bash
docker-compose exec app php artisan tinker
```
Then in tinker:
```php
User::create(['name' => 'Admin', 'email' => 'admin@jassmotors.com', 'password' => bcrypt('password')]);
exit
```

### CI/CD Deployment

Once GitHub secrets are configured:
- Push to `main` or `master` branch
- GitHub Actions automatically deploys to VPS
- Containers rebuild and restart
- Migrations run automatically

## 📱 Access Application

- **Direct**: http://your-vps-ip:8080
- **With Domain**: Configure Nginx reverse proxy (see DEPLOYMENT.md)

## 🛠️ Management Commands

```bash
# View logs
docker-compose logs -f app

# Restart application
docker-compose restart

# Run migrations
docker-compose exec app php artisan migrate

# Clear caches
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# Access container shell
docker-compose exec app bash
```

## 📚 Documentation

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed deployment instructions including:
- Complete VPS setup
- Nginx reverse proxy configuration
- SSL certificate setup
- Troubleshooting guide

## 🔧 Tech Stack

- **Backend**: Laravel 12 (PHP 8.4)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **PDF**: DomPDF
- **Database**: MySQL
- **Container**: Docker & Docker Compose
- **CI/CD**: GitHub Actions

## 📝 Configuration

### Settings Panel Features
- Business name, address, contact info
- Login page logo upload
- Favicon upload
- Invoice footer customization
- All changes reflect immediately in PDFs

### Invoice Features
- VAT included in prices (25.5%)
- Automatic subtotal calculation
- Euro (€) currency
- Professional PDF generation
- Draft and Final invoice states

## 🔐 Security

- Authentication required for all admin functions
- File upload validation
- Environment-based configuration
- Production-ready Docker setup

## 📊 Dashboard Metrics

- Total Sales (All Time)
- Current Month Sales
- Previous Month Sales
- Invoice counts and statistics

## 🆘 Support

For deployment issues:
1. Check Docker logs: `docker-compose logs -f`
2. Check Laravel logs: `docker-compose exec app cat storage/logs/laravel.log`
3. Review [DEPLOYMENT.md](DEPLOYMENT.md) troubleshooting section

## 🎉 Credits

Developed for Jass Motors Invoice Management System
