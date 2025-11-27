# ========================================
# DOCKER DEPLOYMENT GUIDE
# Kyna English - Student Evaluation System
# ========================================

## 🐳 Prerequisites

- Docker Desktop installed
- Docker Compose installed
- Git (optional)

## 🚀 Quick Start

### Step 1: Build and Run

```bash
# Clone or navigate to project directory
cd c:\xampp\htdocs\webstieenghlish

# Build and start containers
docker-compose up -d --build
```

### Step 2: Access Application

- **Web Application:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
  - Server: db
  - Username: root
  - Password: root_password_change_this

### Step 3: Test

1. Open http://localhost:8080
2. Create a new evaluation
3. Test PDF generation

---

## 🔧 Configuration

### Environment Variables

Copy `.env.example` to `.env` and update:

```bash
cp .env.example .env
```

Edit `.env`:
```env
DB_HOST=db
DB_NAME=kyna_english
DB_USER=kyna_user
DB_PASS=YOUR_STRONG_PASSWORD_HERE

MYSQL_ROOT_PASSWORD=YOUR_ROOT_PASSWORD_HERE

BASE_URL=http://localhost:8080/
```

### Update Config Files

The Docker container uses special config files:

- `config/config.docker.php`
- `config/database.docker.php`

**To use Docker configs, update these files to load Docker versions:**

**Option 1:** Rename files temporarily
```bash
# Backup originals
mv config/config.php config/config.local.php
mv config/database.php config/database.local.php

# Use Docker versions
cp config/config.docker.php config/config.php
cp config/database.docker.php config/database.php
```

**Option 2:** Edit files to check environment
```php
// At the top of config/config.php
if (getenv('DOCKER_ENV') === 'true') {
    require_once __DIR__ . '/config.docker.php';
    return;
}
// ... rest of file
```

---

## 📦 Docker Services

### Web Service
- **Container:** kyna-english-web
- **Port:** 8080
- **Base:** PHP 8.1 + Apache
- **Extensions:** PDO, MySQL, GD, ZIP

### Database Service
- **Container:** kyna-english-db
- **Port:** 3306
- **Base:** MariaDB 11.2
- **Database:** kyna_english

### phpMyAdmin Service
- **Container:** kyna-english-phpmyadmin
- **Port:** 8081

---

## 🔍 Troubleshooting

### Issue 1: PDF Generation Fails

**Error:** `Thư mục pdf_output không có quyền ghi`

**Solution:**
```bash
# Stop containers
docker-compose down

# Fix permissions on host
chmod 777 pdf_output
chmod 775 logs

# Restart
docker-compose up -d
```

**Or inside container:**
```bash
docker exec -it kyna-english-web bash
chmod 777 /var/www/html/pdf_output
chmod 775 /var/www/html/logs
chown -R www-data:www-data /var/www/html/pdf_output
chown -R www-data:www-data /var/www/html/logs
exit
```

### Issue 2: Database Connection Failed

**Check database is running:**
```bash
docker-compose ps
```

**Check database logs:**
```bash
docker-compose logs db
```

**Verify credentials:**
- Check `.env` or `docker-compose.yml`
- Ensure `config/database.php` uses correct host: `db` (not `localhost`)

### Issue 3: TCPDF Not Found

**Verify vendor directory:**
```bash
docker exec -it kyna-english-web bash
ls -la /var/www/html/vendor/tcpdf/
```

**If missing, ensure vendor/ is copied:**
```dockerfile
# In Dockerfile, make sure vendor is included
COPY . /var/www/html/
```

### Issue 4: .htaccess Not Working

**Enable mod_rewrite:**
```bash
docker exec -it kyna-english-web bash
a2enmod rewrite
apache2ctl restart
exit
```

### Issue 5: Permission Denied Errors

**Fix all permissions:**
```bash
docker exec -it kyna-english-web bash
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod 777 /var/www/html/pdf_output
chmod 775 /var/www/html/logs
exit
```

---

## 🛠️ Useful Commands

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f web
docker-compose logs -f db
```

### Access Container Shell
```bash
# Web container
docker exec -it kyna-english-web bash

# Database container
docker exec -it kyna-english-db bash
```

### Restart Services
```bash
# All services
docker-compose restart

# Specific service
docker-compose restart web
```

### Stop and Remove
```bash
# Stop containers
docker-compose stop

# Stop and remove
docker-compose down

# Remove with volumes (WARNING: deletes database)
docker-compose down -v
```

### Rebuild
```bash
# Rebuild without cache
docker-compose build --no-cache

# Rebuild and restart
docker-compose up -d --build
```

---

## 📊 Health Check

### Check Container Status
```bash
docker-compose ps
```

Should show:
```
NAME                        STATUS    PORTS
kyna-english-web           Up        0.0.0.0:8080->80/tcp
kyna-english-db            Up        0.0.0.0:3306->3306/tcp
kyna-english-phpmyadmin    Up        0.0.0.0:8081->80/tcp
```

### Check PHP Info
```bash
docker exec kyna-english-web php -v
docker exec kyna-english-web php -m
```

### Check Permissions
```bash
docker exec kyna-english-web ls -la /var/www/html/pdf_output
docker exec kyna-english-web ls -la /var/www/html/logs
```

---

## 🔒 Security for Production

### Change Default Passwords
1. Edit `.env` or `docker-compose.yml`
2. Update `MYSQL_ROOT_PASSWORD`
3. Update `MYSQL_PASSWORD`
4. Rebuild: `docker-compose up -d --build`

### Enable HTTPS
1. Add SSL certificates
2. Update `docker-compose.yml` to include ports 443
3. Configure Apache SSL in Dockerfile

### Restrict Ports
Remove or change port mappings in `docker-compose.yml` for production.

---

## 📝 Production Deployment

### Using Docker Hub

1. **Build and tag image:**
```bash
docker build -t yourusername/kyna-english:latest .
```

2. **Push to Docker Hub:**
```bash
docker login
docker push yourusername/kyna-english:latest
```

3. **On production server:**
```bash
docker pull yourusername/kyna-english:latest
docker-compose up -d
```

### Using Docker Registry

1. **Tag for private registry:**
```bash
docker tag kyna-english registry.example.com/kyna-english:latest
```

2. **Push:**
```bash
docker push registry.example.com/kyna-english:latest
```

---

## 🎯 Summary

**To fix PDF generation error in Docker:**

1. ✅ Ensure `pdf_output/` has 777 permissions
2. ✅ Ensure `vendor/tcpdf/` is copied to container
3. ✅ Check Apache has write permissions (www-data user)
4. ✅ Verify paths in code match container paths
5. ✅ Check PHP memory_limit is sufficient (256M)

**Quick fix command:**
```bash
docker exec -it kyna-english-web bash -c "chmod 777 /var/www/html/pdf_output && chown -R www-data:www-data /var/www/html/pdf_output"
```

---

**For support, check:**
- Container logs: `docker-compose logs -f web`
- Apache error log: `docker exec kyna-english-web cat /var/log/apache2/error.log`
- Application log: `logs/error.log`
