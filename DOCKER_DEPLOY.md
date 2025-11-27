# 🐳 DOCKER DEPLOYMENT GUIDE - VPS Ubuntu

## 📋 PRASYARAT

Sebelum memulai, pastikan sudah terinstall di VPS Ubuntu:

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verifikasi instalasi
docker --version
docker-compose --version
```

---

## 🚀 SETUP DOCKER NETWORK

Karena MySQL sudah ada di container lain, pastikan jaringan `mysql_db` sudah dibuat:

```bash
# Cek network yang sudah ada
docker network ls

# Jika belum ada, buat network
docker network create mysql_db

# Verifikasi network dibuat
docker network inspect mysql_db
```

---

## 📁 STRUKTUR FILE

Pastikan file ini ada di folder project:

```
bisnis/
├── Dockerfile              ✅ Sudah dibuat
├── docker-compose.yaml     ✅ Sudah dibuat
├── nginx.conf              ⏳ HARUS ANDA BUAT SENDIRI
├── backend/
│   ├── config.php          ⚠️  PERLU DIUPDATE
│   ├── auth.php
│   ├── session.php
│   └── ...
├── pages/
├── database/
├── assets/
└── ...
```

---

## ⚙️ UPDATE CONFIG DATABASE

**File: `backend/config.php`**

Update dengan nama container MySQL Anda:

```php
define('DB_HOST', 'mysql');  // Nama service MySQL di docker-compose
define('DB_USER', 'stok_hp');
define('DB_PASS', 'password_mysql_anda');
define('DB_NAME', 'stok_hp');
define('DB_PORT', 3306);
```

**PENTING:** 
- Gunakan nama service MySQL dari `docker-compose.yaml` container Anda
- Pastikan password sesuai dengan container MySQL Anda

---

## 🔧 KONFIGURASI NGINX

**File: `nginx.conf`** (BUAT SENDIRI)

Contoh minimal:

```nginx
upstream php-handler {
    server app:9000;
}

server {
    listen 80;
    server_name _;
    
    root /var/www/html;
    index index.php;

    # Redirect ke index.php untuk routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass php-handler;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ ~$ {
        deny all;
    }
}
```

Letakkan file ini di folder root project (sama level dengan Dockerfile).

---

## 🚢 DEPLOY KE VPS

### Step 1: Upload ke Server

```bash
# Dari local machine
scp -r /path/to/bisnis/ user@vps_ip:/home/user/

# Atau gunakan Git
git clone https://github.com/anda/bisnis.git
cd bisnis
```

### Step 2: SSH ke VPS

```bash
ssh user@vps_ip
cd /path/to/bisnis
```

### Step 3: Build & Run Docker

```bash
# Build image
docker-compose build

# Start container (background)
docker-compose up -d

# Cek status
docker-compose ps

# Lihat logs
docker-compose logs -f app
docker-compose logs -f nginx
```

### Step 4: Initialize Database

```bash
# Exec ke container PHP
docker exec -it stok_hp_app bash

# Test koneksi MySQL
mysql -h mysql -u stok_hp -p stok_hp -e "SHOW TABLES;"

# Import database (jika belum ada)
mysql -h mysql -u stok_hp -p stok_hp < database/database.sql

# Exit container
exit
```

### Step 5: Verify Access

Buka browser dan akses:

```
http://vps_ip:8092
http://vps_ip:8092/pages/login.php
```

---

## 🔐 FIREWALL CONFIGURATION

Buka port 8092 di firewall:

```bash
# UFW (Ubuntu Firewall)
sudo ufw allow 8092
sudo ufw allow 80
sudo ufw allow 443

# Atau jika pakai iptables
sudo iptables -A INPUT -p tcp --dport 8092 -j ACCEPT
```

---

## 📊 DOCKER COMMANDS

### Container Management

```bash
# Status container
docker-compose ps

# Start/Stop/Restart
docker-compose start
docker-compose stop
docker-compose restart

# Logs
docker-compose logs -f
docker-compose logs -f app
docker-compose logs -f nginx

# Execute command
docker exec -it stok_hp_app bash
docker exec -it stok_hp_nginx bash
```

### Volume & Files

```bash
# Copy file dari container
docker cp stok_hp_app:/var/www/html/file.txt ./file.txt

# Copy file ke container
docker cp ./file.txt stok_hp_app:/var/www/html/

# View container file
docker exec stok_hp_app cat /var/www/html/backend/config.php
```

### Network

```bash
# Cek jaringan
docker network inspect mysql_db

# Ping MySQL dari app
docker exec stok_hp_app ping mysql
```

---

## 🐛 TROUBLESHOOTING

### Error 1: "Connection refused"

```bash
# Check if MySQL container running
docker ps | grep mysql

# Test connection
docker exec stok_hp_app mysql -h mysql -u stok_hp -p -e "SELECT 1"
```

**Solusi:**
- Pastikan container MySQL running
- Pastikan network `mysql_db` benar
- Cek DB_HOST di config.php sesuai nama service MySQL

### Error 2: "Cannot connect to MySQL"

```bash
# Verifikasi credentials
docker exec stok_hp_app mysql -h mysql -u stok_hp -p'password' -e "SELECT 1"
```

**Solusi:**
- Update password di `backend/config.php`
- Pastikan password sesuai dengan container MySQL

### Error 3: "502 Bad Gateway"

```bash
# Check PHP-FPM status
docker exec stok_hp_app ps aux | grep php-fpm

# Check logs
docker-compose logs app
docker-compose logs nginx
```

**Solusi:**
- Restart container: `docker-compose restart app`
- Check nginx.conf syntax
- Verifikasi app container berjalan

### Error 4: "Permission denied"

```bash
# Fix permissions
docker exec stok_hp_app chown -R www-data:www-data /var/www/html
docker exec stok_hp_app chmod -R 755 /var/www/html
```

### Error 5: "File not found" saat akses halaman

**Solusi:**
- Pastikan file di folder bisnis sudah ter-sync
- Verifikasi path di nginx.conf
- Check volumes di docker-compose.yaml

---

## 📝 MONITORING & MAINTENANCE

### Check Disk Usage

```bash
# Docker disk usage
docker system df

# Container logs size
docker exec stok_hp_app du -sh /var/www/html
```

### Backup Database

```bash
# Backup dari MySQL container
docker exec mysql_container_name mysqldump -u stok_hp -p stok_hp > backup.sql

# Restore
docker exec -i mysql_container_name mysql -u stok_hp -p stok_hp < backup.sql
```

### Update Application

```bash
# Pull latest code dari Git
cd /path/to/bisnis
git pull origin main

# Rebuild container
docker-compose build --no-cache
docker-compose up -d

# Verify
docker-compose logs -f
```

---

## 🔒 PRODUCTION CHECKLIST

Sebelum go live:

- [ ] Update `backend/config.php` dengan DB credentials benar
- [ ] Set `error_reporting(0)` di production
- [ ] Enable HTTPS (setup SSL certificate)
- [ ] Setup nginx.conf dengan SSL
- [ ] Configure firewall dengan benar
- [ ] Setup backup database regular
- [ ] Monitor disk space
- [ ] Setup log rotation
- [ ] Enable email notifications untuk errors
- [ ] Test database connection
- [ ] Test login functionality
- [ ] Test semua features

---

## 📚 DOCKER COMPOSE YAML PENJELASAN

```yaml
version: '3.8'                    # Docker Compose version

services:
  app:                            # Service PHP-FPM
    build:
      context: .                  # Build dari current dir
      dockerfile: Dockerfile      # Gunakan Dockerfile
    container_name: stok_hp_app   # Nama container
    restart: unless-stopped       # Auto-restart jika crash
    working_dir: /var/www/html    # Working directory
    volumes:
      - ./:/var/www/html          # Mount folder bisnis ke container
    networks:
      - mysql_db                  # Connect ke network mysql_db
    depends_on:
      - mysql                     # Tunggu MySQL start
    expose:
      - 9000                      # Expose port 9000 untuk nginx

  nginx:                          # Service Nginx
    image: nginx:alpine           # Gunakan official nginx image
    container_name: stok_hp_nginx # Nama container
    restart: unless-stopped
    ports:
      - "8092:80"                 # Map port VPS 8092 ke container 80
    volumes:
      - ./:/var/www/html
      - ./nginx.conf:/etc/nginx/conf.d/default.conf:ro  # Mount nginx config
    depends_on:
      - app
    networks:
      - mysql_db

networks:
  mysql_db:                       # Use existing network
    external: true                # Network sudah ada (MySQL container)
```

---

## 🎯 QUICK START COMMAND

```bash
# One-liner setup
docker network create mysql_db && \
docker-compose build && \
docker-compose up -d && \
echo "Akses: http://localhost:8092"
```

---

## ✅ DONE!

Aplikasi sudah running di:
- **Web**: `http://vps_ip:8092`
- **Logs**: `docker-compose logs -f`
- **SSH Container**: `docker exec -it stok_hp_app bash`

Untuk update atau maintenance, gunakan docker commands di atas.

**Happy Deployment! 🚀**
