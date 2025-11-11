# MySQL Configuration for Docker Container

## Step 1: Install MySQL on VPS (if not installed)

```bash
sudo apt update
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql
sudo mysql_secure_installation
```

## Step 2: Create Database and User

```bash
sudo mysql -u root -p
```

In MySQL console:

```sql
-- Create database
CREATE DATABASE jassmotors;

-- Create user for localhost (direct VPS access)
CREATE USER 'jassmotors'@'localhost' IDENTIFIED BY 'GdcYz8R7zbBMafBK';

-- Create user for Docker network (172.x.x.x is Docker's default subnet)
CREATE USER 'jassmotors'@'172.%' IDENTIFIED BY 'GdcYz8R7zbBMafBK';

-- Grant privileges
GRANT ALL PRIVILEGES ON jassmotors.* TO 'jassmotors'@'localhost';
GRANT ALL PRIVILEGES ON jassmotors.* TO 'jassmotors'@'172.%';

-- Apply changes
FLUSH PRIVILEGES;

-- Verify users created
SELECT user, host FROM mysql.user WHERE user = 'jassmotors';

EXIT;
```

## Step 3: Configure MySQL to Listen on All Interfaces

Edit MySQL configuration:

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Find the line:
```
bind-address = 127.0.0.1
```

Change to:
```
bind-address = 0.0.0.0
```

Or comment it out:
```
# bind-address = 127.0.0.1
```

Save and restart MySQL:

```bash
sudo systemctl restart mysql
```

## Step 4: Verify MySQL is Listening

```bash
sudo netstat -tlnp | grep mysql
# Should show: 0.0.0.0:3306 or :::3306
```

## Step 5: Test Connection from Container

After starting your Docker container:

```bash
# From VPS host
cd /var/www/jassmotors

# Test database connection
docker-compose exec app php artisan migrate

# Or enter container and test
docker-compose exec app bash

# Inside container, test MySQL connection:
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::select('SELECT DATABASE()');
```

## Troubleshooting

### Connection Refused Error

If you see "Connection refused":

1. **Check MySQL is running:**
   ```bash
   sudo systemctl status mysql
   ```

2. **Check MySQL is listening on 0.0.0.0:**
   ```bash
   sudo netstat -tlnp | grep 3306
   ```

3. **Check firewall (if enabled):**
   ```bash
   sudo ufw status
   sudo ufw allow 3306/tcp  # Only if needed
   ```

### Access Denied Error

If you see "Access denied":

1. **Verify user exists:**
   ```bash
   sudo mysql -u root -p
   SELECT user, host FROM mysql.user WHERE user = 'jassmotors';
   ```

2. **Recreate user with correct permissions:**
   ```sql
   DROP USER IF EXISTS 'jassmotors'@'172.%';
   CREATE USER 'jassmotors'@'172.%' IDENTIFIED BY 'GdcYz8R7zbBMafBK';
   GRANT ALL PRIVILEGES ON jassmotors.* TO 'jassmotors'@'172.%';
   FLUSH PRIVILEGES;
   ```

3. **Test connection from container:**
   ```bash
   docker-compose exec app php -r "new PDO('mysql:host=host.docker.internal;dbname=jassmotors', 'jassmotors', 'GdcYz8R7zbBMafBK');"
   ```

### Container Can't Resolve host.docker.internal

If DNS resolution fails:

1. **Check extra_hosts in docker-compose.yml:**
   ```yaml
   extra_hosts:
     - "host.docker.internal:host-gateway"
   ```

2. **Manually add to /etc/hosts in container:**
   ```bash
   docker-compose exec app bash
   echo "$(ip route | awk '/default/ {print $3}') host.docker.internal" >> /etc/hosts
   ```

3. **Alternative: Use VPS IP directly:**
   
   In `.env`:
   ```env
   DB_HOST=YOUR_VPS_IP
   ```

## Alternative: MySQL in Docker (Not Recommended for Production)

If you prefer MySQL in Docker (loses data on container removal):

```yaml
services:
  mysql:
    image: mysql:8.0
    container_name: jassmotors-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: jassmotors
      MYSQL_USER: jassmotors
      MYSQL_PASSWORD: GdcYz8R7zbBMafBK
      MYSQL_ROOT_PASSWORD: root_password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - jassmotors-network

  app:
    # ... existing config
    environment:
      - DB_HOST=mysql  # Change from host.docker.internal
    depends_on:
      - mysql

volumes:
  mysql_data:
```

## Quick Connection Test

```bash
# From VPS, test MySQL is accessible
mysql -u jassmotors -p jassmotors
# Enter password: GdcYz8R7zbBMafBK

# From Docker container
docker-compose exec app php artisan db:show

# Check connection details
docker-compose exec app php artisan tinker
>>> DB::connection()->getDatabaseName();
>>> DB::connection()->getDriverName();
```

## After MySQL is Connected

Run migrations to create tables:

```bash
cd /var/www/jassmotors

# Generate application key (IMPORTANT!)
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate --force

# Create admin user
docker-compose exec app php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@jassmotors.com', 'password' => bcrypt('your_password')]);
```

Your application should now be fully functional at `http://your-vps-ip:8080`! 🚀
