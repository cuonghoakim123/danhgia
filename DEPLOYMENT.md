# ========================================
# DEPLOYMENT CHECKLIST
# 123 English - Student Evaluation System
# ========================================

## Pre-Deployment Checklist

### 1. Server Requirements
- [x] PHP >= 7.4
- [x] MySQL/MariaDB >= 5.7
- [x] Apache with mod_rewrite enabled
- [x] PHP Extensions: PDO, PDO_MySQL, mbstring, gd (optional for images)

### 2. File Permissions
```bash
# Set proper permissions
chmod 755 -R .
chmod 644 *.php
chmod 644 config/*.php
chmod 777 pdf_output/
chmod 644 .htaccess
```

### 3. Configuration Files

#### config/config.php
- [ ] Update BASE_URL for production domain
- [ ] Set error_reporting(0) for production
- [ ] Set display_errors to 0
- [ ] Enable error logging
- [ ] Update contact information if needed
- [ ] Set session.cookie_secure to 1 if using HTTPS

#### config/database.php
- [ ] Update DB_HOST (production database host)
- [ ] Update DB_NAME (production database name)
- [ ] Update DB_USER (production database user)
- [ ] Update DB_PASS (production database password - STRONG password!)

### 4. Database Setup
```bash
# Import database schema
mysql -u username -p database_name < database/schema.sql
```

- [ ] Database created
- [ ] Schema imported successfully
- [ ] Sample data loaded (optional)
- [ ] Database user has proper permissions
- [ ] Test database connection

### 5. Dependencies
- [x] TCPDF library installed in vendor/tcpdf/
- [ ] Run `composer install --no-dev` for production (if using Composer)
- [x] All required PHP files present

### 6. Security Configuration

#### .htaccess Files
- [x] Root .htaccess created
- [x] config/.htaccess created (deny access)
- [x] pdf_output/.htaccess created (allow only PDFs)

#### Security Measures
- [ ] Change default database credentials
- [ ] Remove or protect setup.sh
- [ ] Remove test/sample data from production
- [ ] Enable HTTPS (uncomment in .htaccess)
- [ ] Set secure session cookies
- [ ] Review file permissions

### 7. Testing Before Go-Live

#### Functionality Tests
- [ ] Test index.php loads correctly
- [ ] Test student search by code
- [ ] Test creating new evaluation
- [ ] Test PDF generation
- [ ] Test list.php - view evaluations
- [ ] Test edit.php - edit evaluation
- [ ] Test preview.php - preview before PDF
- [ ] Test reports.php - view reports
- [ ] Test 404 error page
- [ ] Test API endpoints:
  - [ ] api/get_student.php
  - [ ] api/get_courses.php
  - [ ] api/save_evaluation.php

#### Security Tests
- [ ] Try accessing config/config.php directly (should be denied)
- [ ] Try accessing config/database.php directly (should be denied)
- [ ] Check that error messages don't reveal sensitive info
- [ ] Test CSRF protection
- [ ] Test input sanitization

### 8. Performance Optimization
- [ ] Enable GZIP compression
- [ ] Enable browser caching
- [ ] Optimize images (if any)
- [ ] Test page load times
- [ ] Consider CDN for assets (optional)

### 9. Backup & Recovery
- [ ] Set up automated database backups
- [ ] Document backup locations
- [ ] Test restore procedure
- [ ] Keep backup of schema.sql

### 10. Monitoring & Maintenance
- [ ] Set up error logging
- [ ] Monitor log files regularly
- [ ] Set up disk space monitoring (for pdf_output/)
- [ ] Plan for regular updates
- [ ] Document admin procedures

## Deployment Steps

### Step 1: Prepare Server
```bash
# Upload files via FTP/SFTP or git
# Exclude: .git/, .vscode/, *.md (except README.md)
```

### Step 2: Set Permissions
```bash
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 777 pdf_output/
```

### Step 3: Configure
- Edit config/config.php
- Edit config/database.php

### Step 4: Import Database
```bash
mysql -u user -p database < database/schema.sql
```

### Step 5: Test
- Access the site
- Test all features
- Check error logs

### Step 6: Go Live
- Update DNS if needed
- Enable HTTPS
- Monitor for errors

## Post-Deployment

### Monitoring
- Check error logs: logs/error.log
- Monitor database size
- Monitor pdf_output/ directory size

### Maintenance
- Regular database backups
- Regular log cleanup
- Update PHP/MySQL as needed
- Security patches

## Troubleshooting

### Common Issues

1. **White screen/blank page**
   - Check PHP error log
   - Verify file permissions
   - Check database connection

2. **Database connection error**
   - Verify credentials in config/database.php
   - Check MySQL service is running
   - Verify database exists

3. **PDF generation fails**
   - Check pdf_output/ permissions (777)
   - Verify TCPDF is installed
   - Check PHP memory_limit

4. **404 errors**
   - Verify .htaccess is uploaded
   - Check mod_rewrite is enabled
   - Verify RewriteBase path

5. **Session issues**
   - Check session.save_path permissions
   - Verify session settings in php.ini

## Support & Documentation

- System Version: 1.0.0
- PHP Version Required: >= 7.4
- Database: MySQL/MariaDB >= 5.7

For issues, check:
1. Error logs
2. PHP error log
3. Apache error log
4. Browser console

## Production Configuration Summary

### Must Change for Production:
1. Database credentials
2. Error reporting (disable display_errors)
3. Enable HTTPS
4. Set secure session cookies
5. Strong database password
6. Remove development files

### Performance Settings:
- memory_limit: 256M
- upload_max_filesize: 10M
- max_execution_time: 300
- Enable GZIP compression
- Enable browser caching

### Security Settings:
- Deny access to config/
- Restrict pdf_output/ to PDFs only
- Set security headers
- Disable directory listing
- Hide server signature
