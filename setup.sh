#!/bin/bash

# JassMotors Invoice - Quick Setup Script
# Run this script on your VPS after cloning the repository

set -e

echo "🚀 JassMotors Invoice - Setup Script"
echo "======================================"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo "⚠️  Please don't run as root. Run as regular user with sudo privileges."
    exit 1
fi

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Installing..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    echo "✅ Docker installed. Please logout and login again, then re-run this script."
    exit 0
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Installing..."
    sudo apt install docker-compose -y
fi

echo "✅ Docker and Docker Compose are installed"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    
    echo ""
    echo "⚠️  IMPORTANT: Please edit .env file with your database credentials:"
    echo "   nano .env"
    echo ""
    echo "Update these values:"
    echo "   DB_HOST=host.docker.internal"
    echo "   DB_DATABASE=jassmotors"
    echo "   DB_USERNAME=your_mysql_user"
    echo "   DB_PASSWORD=your_mysql_password"
    echo "   APP_URL=http://your-domain.com"
    echo ""
    read -p "Press Enter after you've updated the .env file..."
fi

# Build and start containers
echo "=========================================="
echo "Building Docker containers..."
echo "=========================================="
docker-compose build --no-cache

echo ""
echo "=========================================="
echo "Starting containers..."
echo "=========================================="
docker-compose up -d

echo ""
echo "=========================================="
echo "Installing dependencies..."
echo "=========================================="
docker-compose exec app composer install --no-dev --optimize-autoloader

echo ""
echo "=========================================="
echo "Generating application key..."
echo "=========================================="
docker-compose exec app php artisan key:generate --force

# Generate application key if needed
echo "🔑 Generating application key..."
docker-compose exec -T app php artisan key:generate --force || true

# Run migrations
echo "📊 Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Create storage link
echo "🔗 Creating storage symlink..."
docker-compose exec -T app php artisan storage:link || true

# Cache configuration
echo "💾 Caching configuration..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Set permissions
echo "🔒 Setting proper permissions..."
docker-compose exec -T app chown -R www-data:www-data /var/www/html/storage
docker-compose exec -T app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec -T app chmod -R 775 /var/www/html/storage
docker-compose exec -T app chmod -R 775 /var/www/html/bootstrap/cache

echo ""
echo "🎉 Setup completed successfully!"
echo "======================================"
echo ""
echo "📱 Your application is now running at:"
echo "   http://$(hostname -I | awk '{print $1}'):8080"
echo ""
echo "📝 Next steps:"
echo "   1. Create an admin user: docker-compose exec app php artisan tinker"
echo "      Then run: User::create(['name' => 'Admin', 'email' => 'admin@jassmotors.com', 'password' => bcrypt('password')]);"
echo "   2. Access the application and change the default password"
echo "   3. Configure settings in the admin panel"
echo ""
echo "🔧 Useful commands:"
echo "   View logs: docker-compose logs -f app"
echo "   Restart: docker-compose restart"
echo "   Stop: docker-compose down"
echo ""
