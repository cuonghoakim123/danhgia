# Kyna English - Database Setup Guide

## Option 1: Using phpMyAdmin (Recommended for Beginners)

### Step 1: Open phpMyAdmin
1. Start XAMPP Control Panel
2. Start Apache and MySQL
3. Open browser and go to: http://localhost/phpmyadmin

### Step 2: Create Database
1. Click "New" in the left sidebar
2. Database name: `kyna_english`
3. Collation: `utf8mb4_unicode_ci`
4. Click "Create"

### Step 3: Import Schema
1. Click on `kyna_english` database in left sidebar
2. Click "Import" tab at the top
3. Click "Choose File" button
4. Select: `database/schema.sql`
5. Click "Go" at the bottom
6. Wait for success message

### Step 4: Verify Setup (Optional)
1. Click "SQL" tab
2. Copy and paste contents of `database/verify_setup.sql`
3. Click "Go"
4. You should see confirmation messages

---

## Option 2: Using MySQL Command Line

### Windows
```bash
# Open Command Prompt
cd C:\xampp\mysql\bin

# Login to MySQL
mysql.exe -u root -p

# Create database
CREATE DATABASE kyna_english CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Use database
USE kyna_english;

# Import schema
SOURCE C:/xampp/htdocs/webstieenghlish/database/schema.sql;

# Verify
SELECT COUNT(*) FROM courses;
SELECT COUNT(*) FROM evaluation_criteria;

# Exit
EXIT;
```

### Linux/Mac
```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE kyna_english CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Use database
USE kyna_english;

# Import schema
SOURCE /path/to/webstieenghlish/database/schema.sql;

# Verify
SELECT COUNT(*) FROM courses;
SELECT COUNT(*) FROM evaluation_criteria;

# Exit
EXIT;
```

---

## Option 3: Automated Setup (Advanced)

Create a PHP setup script:

```php
<?php
// setup.php - Place in project root

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'kyna_english';

// Create database
$conn = new mysqli($host, $user, $pass);
$conn->query("CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->close();

// Import schema
$conn = new mysqli($host, $user, $pass, $db);
$sql = file_get_contents('database/schema.sql');

// Execute multi-query
$conn->multi_query($sql);
do {
    if ($result = $conn->store_result()) {
        $result->free();
    }
} while ($conn->next_result());

echo "✓ Database setup complete!";
$conn->close();
```

Run: `http://localhost/webstieenghlish/setup.php`

---

## Troubleshooting

### Error: Access denied
**Problem**: Cannot connect to MySQL

**Solution**:
- Check MySQL is running in XAMPP
- Verify username/password in `config/database.php`
- Default: user=`root`, password=`` (empty)

### Error: Database already exists
**Problem**: Database name conflict

**Solution**:
1. Open phpMyAdmin
2. Drop existing `kyna_english` database
3. Import schema.sql again

### Error: Import failed
**Problem**: SQL file too large or timeout

**Solution**:
1. Open `php.ini` in XAMPP
2. Increase:
   ```
   max_execution_time = 300
   max_input_time = 300
   memory_limit = 512M
   post_max_size = 128M
   upload_max_filesize = 128M
   ```
3. Restart Apache
4. Try import again

### Error: Tables not created
**Problem**: SQL execution failed

**Solution**:
- Check MySQL error log: `C:\xampp\mysql\data\*.err`
- Try importing via command line instead
- Check file encoding is UTF-8

---

## Default Data Included

After import, you will have:

### Courses (5 items)
- Daily English - Cấp độ DE Beginner 1
- Daily English - Cấp độ DE Beginner 2
- Daily English - Cấp độ DE Pre-Inter 1
- Daily English - Cấp độ DE Pre-Inter 2
- Daily English - Cấp độ DE Intermediate 1

### Evaluation Criteria
- **Strengths**: 5 criteria
- **Improvements**: 4 criteria

### Sample Students (3 items)
- Test data for development

You can add/edit/delete these through the "Quản Lý Dữ Liệu" page.

---

## Backup & Restore

### Backup Database
```bash
# phpMyAdmin: Export tab > Go
# Or command line:
mysqldump -u root -p kyna_english > backup_$(date +%Y%m%d).sql
```

### Restore from Backup
```bash
# phpMyAdmin: Import tab > Choose file
# Or command line:
mysql -u root -p kyna_english < backup_20240101.sql
```

---

## Need Help?

Contact: hotro@kynaforkids.vn

